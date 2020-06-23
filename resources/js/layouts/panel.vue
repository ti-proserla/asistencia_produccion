<template>
    <div id="app" :class="(statusSidebar) ? 'open':''">
        <div class="sidebar" >
            <administrador></administrador>
            <div class="text-center mb-3">
                <button @click="cerrar()" class="btn btn-danger btn-sm">Salir</button>
            </div>
        </div>
        <div class="background-sidebar" @click="close()"></div>
        <nav class="navbar">
            <button @click="open()" class="btn-link-success"><i class="material-icons">menu</i></button>
            <div>
                <h5>Sist. Asistencia y Tareo - {{ fundo }}</h5>
            </div>
            <span v-if="conexion" class="material-icons text-success">
                cloud_queue
            </span>
            <span v-else class="material-icons text-danger">
                cloud_off
            </span>
        </nav>
        <div class="content">
            <slot/>
        </div>
        <div class="footer">
            {{ (empresa=='PROSERLA') ? 'Proserla @ Area de TI' : 'Jayanca Fruits & Proserla @ Area de TI'}}
            
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'
import administrador from './parts/administrador.vue'
import comun from './parts/comun.vue'

export default {
    data() {
        return {
            empresa: 'PROSERLA'
        }
    },
    components:{
        administrador,
        comun
    },
    computed: {
        ...mapState(['cuenta','fundo','conexion']),
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