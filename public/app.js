// prosta nav
document.querySelectorAll('nav button').forEach(btn=>{
  btn.onclick = e=>{
    document.querySelectorAll('main section').forEach(s=>s.hidden=true);
    document.getElementById(btn.dataset.target).hidden=false;
  };
});
// start od listy zleceń
document.querySelector('button[data-target="orders"]').click();

async function api(method, url, data){
  const opts = { method, headers:{} };
  if(data){
    opts.headers['Content-Type']='application/json';
    opts.body=JSON.stringify(data);
  }
  const res = await fetch(url, opts);
  return res.json();
}

// 1. Lista zleceń
async function loadOrders(){
  const orders = await api('GET','/api/queries/orders');
  const body = document.getElementById('orders-body');
  body.innerHTML = '';
  orders.forEach(o=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`<td>${o.order_id}</td><td>${o.status}</td><td>${o.created_at}</td>`;
    body.appendChild(tr);
  });
}
document.getElementById('reload-orders').onclick=loadOrders;

// 2. Nowe zlecenie
document.getElementById('form-new-order').onsubmit = async e=>{
  e.preventDefault();
  const data=Object.fromEntries(new FormData(e.target));
  const res=await api('POST','/api/commands/submit-order',data);
  document.getElementById('new-order-result').textContent=JSON.stringify(res,null,2);
  loadOrders();
};

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
