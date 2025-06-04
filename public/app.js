import { Input, Autocomplete, Select, Modal, Ripple, Dropdown, Collapse, initMDB } from "./mdb/js/mdb.es.min.js";
initMDB({ Input, Autocomplete, Select, Modal, Ripple, Dropdown, Collapse });

'use strict';

const getCookie = (name) => {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
};

const setCookie = (name, value, days = 7) => {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
};

const user = (() => {
    let u = getCookie('user_spedycja');
    if (!u) {
        u = "Klient";
        setCookie('user_spedycja', u);
    }
    return u;
})();

document.addEventListener('DOMContentLoaded', () => {
    loadOrders();

    document.getElementById('reload-orders').onclick = loadOrders;

    // Dodawanie nowego zlecenia
    document.getElementById("nav-addForm").addEventListener("click", async () => {
        showModal("views/addForm.php").then(loadContent).then(() => {
            const autocompleteData = [];

            const asyncAutocomplete = document.querySelector('#async');
            const asyncFilter = async (query) => {
                const url = `/api/queries/companies?search=${encodeURI(query)}`;
                const response = await fetch(url);
                const data = await response.json();
                return data;
            };

            const dataFilter = (value) => {
                return autocompleteData.filter((item) => {
                    return item.toLowerCase().startsWith(value.toLowerCase());
                });
            };

            asyncFilter('').then(data => {
                autocompleteData.push(...data);
                new Autocomplete(asyncAutocomplete, {
                    filter: dataFilter,
                    container: "#modalForm"
                });
            });

            const form = document.querySelector("#form-new-order");
            form.onsubmit = async e => {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(form));
                const res = await api('POST', '/api/commands/submit-order', data);
                log(res);
                if (res.error) {
                    return;
                }
                loadOrders();
                hideModal();
            };
        });
    });

    // Select usera
    const selectUser = document.getElementById('select-user');
    Select.getInstance(selectUser).setValue(user);
    selectUser.addEventListener('change', async () => {
        const userId = selectUser.value;
        const res = await api('POST', '/api/commands/set-user', { user: userId });
        log(res);
        if (res.error) {
            return;
        }
        window.location.reload();
    });
});

async function api(method, url, data) {
    const opts = { method, headers:{} };
    if (data) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(data);
    }
    const res = await fetch(url, opts);
    const text = await res.text();
    try {
        return JSON.parse(text);
    }
    catch (e) {
        console.error('API response is not valid JSON:', text);
        return null;
    }
}

// Lista zleceń
async function loadOrders() {
    const orders = await api('GET','/api/queries/orders');
    const body = document.getElementById('orders-body');
    body.innerHTML = '';
    orders.forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${o.ID_order}</td><td>${o.status}</td><td>${o.company}</td><td>${o.location_from}</td><td>${o.location_to}</td><td>${o.cargo}</td><td>${o.weight}kg</td><td>${o.driver ?? "-"}</td><td>${o.created_at}</td>`;
        tr.addEventListener('click', () => { detailsOrder(o) });
        body.appendChild(tr);
    });
}

// Szczegóły zlecenia
async function detailsOrder(order) {
    showModal(`views/orderDetails.php?order=${JSON.stringify(order)}`).then(loadContent).then(() => {

        const select = document.getElementById('driverId')
        if (select) {
            new Select(select, {
                container: "#modalForm",
            });
        }

        const form = document.querySelector("#form-details");
        form.onsubmit = async e => {
            e.preventDefault();
            let res;

            // Sprawdzenie statusu zlecenia i wykonanie odpowiedniej akcji
            if (order.status === 'Utworzone') {
                const data = Object.fromEntries(new FormData(e.target));
                console.log(data);
                res = await api('POST', '/api/commands/validate-order', data);
            }
            else if (order.status === 'Zatwierdzone przez spedytora' && user === 'Spedytor') {
                if (select.value === '') {
                    alert('Proszę wybrać kierowcę.');
                    return;
                }

                const data = Object.fromEntries(new FormData(e.target));
                res = await api('POST', '/api/commands/assign-driver', data);
            }
            else if (order.status === 'Zatwierdzone przez spedytora' && user === 'Klient') {
                const data = Object.fromEntries(new FormData(e.target));
                res = await api('POST', '/api/commands/accept-offer', data);
            }

            log(res);
            if (res.error) {
                return;
            }
            loadOrders();
            hideModal();
        };
    });
}



// Funkcje pomocnicze
async function showModal(url, id = 'modalForm') {
    const modal = document.getElementById(id);
    if (modal) {
        modal.innerHTML = '';
        const res = await fetch(url);
        const html = await res.text();
        modal.innerHTML = html;
        const instance = new Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });
        instance.show();
    }
}

async function hideModal(id = 'modalForm') {
    const modal = document.getElementById(id);
    if (modal) {
        const instance = Modal.getInstance(modal);
        instance.hide();
        setTimeout(() => {
            instance.dispose();
        }, 500);
    }
}

async function log(message) {
    console.log("Wynik operacji: ", message);
}

async function loadContent() {
    document.querySelectorAll(".form-outline:not([data-mdb-input-initialized='true'])").forEach(input => {
        new Input(input);
    });
}



// 4. Widok kierowców
// Można dodać na select do pobierania z selecta kierowcy i pokazywania przypisania

// document.getElementById('form-driver-view').onsubmit = async e=>{
//     e.preventDefault();
//     const {driverId}=Object.fromEntries(new FormData(e.target));
//     const list=await api('GET',`/api/queries/driver-assign?driverId=${driverId}`);
//     const body=document.getElementById('driver-body');
//     body.innerHTML='';
//     list.forEach(a=>{
//         const tr=document.createElement('tr');
//         tr.innerHTML=`<td>${a.order_id}</td><td>${a.status}</td>`;
//         body.appendChild(tr);
//     });
// };