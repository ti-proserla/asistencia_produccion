import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)
/**
 * ROUTER VUE
 */
var auth=(to, from,next)=>{
    $('.modal-backdrop').remove();
    store.state.sidebar.statusSidebar=false;
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
        component: require('../view/Operacion/marcador.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/tareo', 
        component: require('../view/Operacion/tareo.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/regularizar', 
        component: require('../view/Operacion/regularizar.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/cuenta', 
        component: require('../view/Cuenta/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/operador', 
        component: require('../view/Operador/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/planilla', 
        component: require('../view/Planilla/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/cargo', 
        component: require('../view/Cargo/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/boleta', 
        component: require('../view/Reportes/boleta.vue').default,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/login', 
        component: require('../view/login.vue').default,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/consulta/marcas', 
        component: require('../view/consulta/marcas').default,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/fotocheck-frame', 
        component: require('../view/Operador/fotocheck-frame.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/code-frame/:codigo/:nom_ape', 
        component: require('../view/Operador/code-frame.vue').default,
        beforeEnter: auth,
        meta:{
            layout: "empty",
        },
    },
    { 
        path: '/fotocheck', 
        component: require('../view/Operador/fotocheck.vue').default,
        meta:{
            layout: "empty",
        },
        beforeEnter: auth
    },
    { 
        path: '/proceso', 
        component: require('../view/Proceso/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/fundo', 
        component: require('../view/Fundo/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/area', 
        component: require('../view/Area/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/labor', 
        component: require('../view/Labor/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/turno', 
        component: require('../view/Turno/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/linea', 
        component: require('../view/Linea/index.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-rotaciones', 
        component: require('../view/Reportes/rotaciones.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-semana', 
        component: require('../view/Reportes/semana.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-semana-partida', 
        component: require('../view/Reportes/j.semana-partida.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-pendientes', 
        component: require('../view/Reportes/pendientes.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/reporte-marcas', 
        component: require('../view/Reportes/marcas.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/marcas-noche', 
        component: require('../view/Reportes/marcas-noche.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/rango',
        component: require('../view/Reportes/rango.vue').default,
        beforeEnter: auth
    },
    { 
        path: '/capturador', 
        component: require('../view/capturador.vue').default,
        beforeEnter: auth
    },
    {
        path: '/configuracion',
        component:require('../view/configuracion.vue').default,
        beforeEnter: auth
    },
    // 19/05/2020
    {
        path: '/modulo',
        component:require('../view/Modulo/index.vue').default,
        beforeEnter: auth
    },
    {
        path: '/rol',
        component:require('../view/Rol/index.vue').default,
        beforeEnter: auth
    },
    // 29/05/2020
    {
        path: '/fundo/seleccionar',
        component: require('../view/Fundo/seleccionar.vue').default
    }
];
export default new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});