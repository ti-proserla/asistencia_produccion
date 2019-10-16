/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'
Vue.use(VueRouter)
import VueQuagga from 'vue-quaggajs';

Vue.use(VueQuagga);

var routes =[
    { 
        path: '/scanner', 
        component: require('./view/scanner.vue').default
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
