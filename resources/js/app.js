require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'
Vue.use(VueRouter)
import VueQuagga from 'vue-quaggajs';
Vue.use(VueQuagga);
window.moment = require('moment');

import Croppa from 'vue-croppa'
 Vue.use(Croppa)   

import swal from 'sweetalert';
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
// Vue.component('v-select', VueSelect.VueSelect)
/**
 * Vuex session
 */
import Vuex from 'vuex'
Vue.use(Vuex)

window.store=new Vuex.Store({
    state: {
      cuenta: JSON.parse(localStorage.getItem('cuenta_sistema'))||null
    },
    mutations: {
      auth_success(state,cuenta){
        state.cuenta=cuenta;
        localStorage.setItem('cuenta_sistema',JSON.stringify(state.cuenta));
        axios.defaults.headers.common['Authorization'] = state.cuenta.token;
      },
      auth_close(state){
        state.cuenta=null;
        localStorage.removeItem('cuenta_sistema');
      }
    },
    actions: {}
});
if (store.state.cuenta!=null) {
    axios.defaults.headers.common['Authorization'] = store.state.cuenta.token;
}
/**
 * ROUTER VUE
 */
var auth=(to, from,next)=>{
    $('.modal-backdrop').remove();
    if(store.state.cuenta===null){
        next('/login');
    }else{
        next(); 
    }
}
var routes =[
    {
        path: '/',
        redirect: '/marcador'
    },
    { 
        path: '/marcador', 
        component: require('./view/Operacion/marcador.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/marcador2', 
        component: require('./view/Operacion/marcador2.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/tareo', 
        component: require('./view/Operacion/tareo.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/regularizar', 
        component: require('./view/Operacion/regularizar.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/cuenta', 
        component: require('./view/Cuenta/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/operador', 
        component: require('./view/Operador/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/planilla', 
        component: require('./view/Planilla/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/login', 
        component: require('./view/login.vue').default,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/consulta/marcas', 
        component: require('./view/consulta/marcas').default,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/fotocheck/:id', 
        component: require('./view/Operador/fotocheck.vue').default,
        meta:{
            layout: "empty",
        },
        beforeEnter: auth
    },
    { 
        path: '/proceso', 
        component: require('./view/Proceso/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/area', 
        component: require('./view/Area/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/labor', 
        component: require('./view/Labor/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/turno', 
        component: require('./view/Turno/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/linea', 
        component: require('./view/Linea/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-semana', 
        component: require('./view/Reportes/turno.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-pendientes', 
        component: require('./view/Reportes/pendientes.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-marcas', 
        component: require('./view/Reportes/marcas.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/capturador', 
        component: require('./view/capturador.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/scanner', 
        component: require('./view/scanner.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte', 
        component: require('./view/reporte.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/refresh', 
        component: require('./view/refresh.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/refreshOperario', 
        component: require('./view/refreshOperario.vue').default,
        beforeEnter: auth
    },
];
var router=new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});

import Dashboard from './App.vue';
Vue.component('empty',require("./layouts/empty.vue").default);
Vue.component('panel',require("./layouts/panel.vue").default);


const app = new Vue({
    el: '#app',
    router,
    store,
    render: h => h(Dashboard)
});
