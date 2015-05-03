function init_dom_for_lk_page(dom) {
  var dom = {
    blocks: {
      pending_orders_list: document.getElementById('pending-orders-list'),
      completed_orders_list: document.getElementById('completed-orders-list')
    },
    labels: {
      balance: document.getElementById('profile-balance'),
      pending_orders_cnt: document.getElementById('pending-orders-cnt'),
      completed_orders_cnt: document.getElementById('completed-orders-cnt')
    }
  };
  return dom;
}