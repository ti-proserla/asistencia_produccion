require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'
Vue.use(VueRouter)
import VueQuagga from 'vue-quaggajs';
Vue.use(VueQuagga);
window.moment = require('moment');

var routes =[
    { 
        path: '/operador', 
        component: require('./view/operador.vue').default
    },
    { 
        path: '/capturador', 
        component: require('./view/capturador.vue').default
    },
    { 
        path: '/scanner', 
        component: require('./view/scanner.vue').default
    },
    { 
        path: '/reporte', 
        component: require('./view/reporte.vue').default
    },
    { 
        path: '/refresh', 
        component: require('./view/refresh.vue').default
    },
    { 
        path: '/refreshOperario', 
        component: require('./view/refreshOperario.vue').default
    },
];
var router=new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});

import Dashboard from './App.vue';

const app = new Vue({
    el: '#app',
    router,
    render: h => h(Dashboard)
});
