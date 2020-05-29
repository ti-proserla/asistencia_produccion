<template>
    <div id="app" :class="(statusSidebar) ? 'open':''">
        <div class="sidebar" >
            <administrador></administrador>
        </div>
        <div class="background-sidebar" @click="close()"></div>
        <nav class="navbar">
            <button v-if="cuenta.rol!='COMUN'" @click="open()" class="btn-link-success"><i class="material-icons">menu</i></button>
            <div>
                <h5>Sist. Asistencia y Tareo - {{ fundo }}</h5>
            </div>
            <button @click="cerrar()" class="btn btn-danger btn-sm btn-float-right">Salir</button>
        </nav>
        <div class="content">
            <slot/>
        </div>
        <div class="footer">
            Jayanca Fruits & Proserla @ Area de TI
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'
import administrador from './parts/administrador.vue'
import comun from './parts/comun.vue'

export default {
    components:{
        administrador,
        comun
    },
    computed: {
        ...mapState(['cuenta','fundo']),
        ...mapState('sidebar',['statusSidebar']),
    },
    mounted() {
        console.log(this.cuenta);
    },
    methods: {
        ...mapMutations('sidebar',['open','close']),
        url(){},
        cerrar(){
            this.$store.commit('auth_close');
            this.$router.push({path: "/login"} );
        }
    },
}
</script>