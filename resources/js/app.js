require('./bootstrap');

window.Vue = require('vue');

import VueQuagga from 'vue-quaggajs';
Vue.use(VueQuagga);
window.moment = require('moment');

import Croppa from 'vue-croppa'
 Vue.use(Croppa)   

import swal from 'sweetalert';
import vSelect from 'vue-select'
Vue.component('v-select', vSelect)

window.mix_empresa=process.env.MIX_EMPRESA||null;
window.db = window.openDatabase("asistencia", "1.0", "appAsistencia", 200000);


// Vue.component('v-select', VueSelect.VueSelect)
/**
 * Vuex session
 */
import Vuex from 'vuex'
Vue.use(Vuex)

const moduleSidebar={
    namespaced: true,
    state:{
        statusSidebar: false
    },
    mutations: {
        open(state){
            state.statusSidebar=true;
        },
        close(state){
            state.statusSidebar=false;
        }
    }
}

window.store=new Vuex.Store({
    state: {
        cuenta: JSON.parse(localStorage.getItem('cuenta_sistema'))||null,
        turno: localStorage.getItem('turno') || null,
        fundo: localStorage.getItem('fundo') || null,
        modulos: JSON.parse(localStorage.getItem('modulos')) || [],
        conexion: false
    },
    modules:{
        'sidebar': moduleSidebar
    },
    mutations: {        
      auth_success(state,cuenta){
        state.cuenta=cuenta;
        localStorage.setItem('cuenta_sistema',JSON.stringify(state.cuenta));
        axios.defaults.headers.common['Authorization'] = state.cuenta.api_token;
        store.commit('getModulos');
      },
      auth_close(state){
        state.cuenta=null;
        localStorage.removeItem('cuenta_sistema');
        localStorage.removeItem('modulos');
      },
      update_turno(state,data){
        state.turno=data;
        localStorage.setItem('turno',data);
      },
      getModulos(state){
        if (state.cuenta!=null) {
            var id=state.cuenta.id;
            axios.get(url_base+'/rol/'+id+'/modulos?name')
            .then(response => {
                state.modulos = response.data;
                localStorage.setItem('modulos',JSON.stringify(state.modulos));
            });
        }
      },
        updateFundo(state,data){
            state.fundo=data;
            localStorage.setItem('fundo',data);
        }
    },
    actions: {}
});
store.state.turno=localStorage.getItem('turno') || null;
if (store.state.cuenta!=null) {
    axios.defaults.headers.common['Authorization'] = store.state.cuenta.api_token;
}


/**
 * Socket IO
 */
var socket = io.connect((process.env.MIX_SOCKET||'http://localhost:8070'), { 'forceNew': true });
socket.on('connect', function(){
    store.state.conexion=true;
});                                 
socket.on('disconnect', function (){
    store.state.conexion=false;
    console.log('disconnected')
});
socket.on('reconnect', function (){
    console.log('reconnect')
    store.state.conexion=true;

});

// import router from './router/jayanca.js'
var router =require('./router/jayanca.js').default;

import Dashboard from './App.vue';
Vue.component('empty',require("./layouts/empty.vue").default);
Vue.component('panel',require("./layouts/panel.vue").default);

const app = new Vue({
    el: '#app',
    router,
    store,
    render: h => h(Dashboard)
});
