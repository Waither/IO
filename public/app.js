import { Input, Modal, Ripple, Dropdown, Collapse, initMDB } from "./mdb/js/mdb.es.min.js";

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initMDB({ Input, Dropdown, Collapse, Modal, Ripple });

    loadOrders();

    document.getElementById('reload-orders').onclick=loadOrders;

    // 3. Przypisanie kierowcy
    document.getElementById('form-assign-driver').onsubmit = async e=>{
        e.preventDefault();
        const data=Object.fromEntries(new FormData(e.target));
        const res=await api('POST','/api/commands/assign-driver',data);
        document.getElementById('assign-driver-result').textContent=JSON.stringify(res,null,2);
    };

    // 4. Widok kierowcy
    document.getElementById('form-driver-view').onsubmit = async e=>{
        e.preventDefault();
        const {driverId}=Object.fromEntries(new FormData(e.target));
        const list=await api('GET',`/api/queries/driver-assign?driverId=${driverId}`);
        const body=document.getElementById('driver-body');
        body.innerHTML='';
        list.forEach(a=>{
            const tr=document.createElement('tr');
            tr.innerHTML=`<td>${a.order_id}</td><td>${a.status}</td>`;
            body.appendChild(tr);
        });
    };

    // Dodawanie nowego zlecenia
    document.getElementById("nav-addForm").addEventListener("click", async () => {
        showModal("views/addForm.php").then(loadContent).then(() => {
            const form = document.querySelector("#form-new-order");
            form.onsubmit = async e => {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(form));
                const res = await api('POST', '/api/commands/submit-order', data);
                log(res);
                loadOrders();
                hideModal();
            };
        });
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
        tr.innerHTML = `<td>${o.ID_order}</td><td>${o.status}</td><td>${o.location_from}</td><td>${o.location_to}</td><td>${o.cargo}</td><td>${o.ID_driver ?? "-"}</td><td>${o.created_at}</td>`;
        tr.addEventListener('click', () => { acceptOrder(o) });
        body.appendChild(tr);
    });

    // document.querySelectorAll('.btn-validate').forEach(btn=>{
    //     btn.onclick = async () => {
    //         const orderId = btn.dataset.id;
    //         const res = await api('POST', '/api/commands/validate-order', { orderId });
    //         console.log(res);
    //         loadOrders();
    //     };
    // });
}

// Akceptacja zlecenia
async function acceptOrder(order) {
    showModal(`views/orderDetails.php?order=${JSON.stringify(order)}`).then(loadContent).then(() => {
        
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