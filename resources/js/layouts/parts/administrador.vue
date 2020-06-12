<template>
    <ul>
        <li v-if="existe('marcador')">
            <router-link to="/marcador">
                <i class="material-icons">airplay</i>
                <p>marcador</p>
            </router-link>
        </li>
        <li v-if="existe('tareo')">
            <router-link to="/tareo">
                <i class="material-icons">airplay</i>
                <p>tareo</p>
            </router-link>
        </li>
        <li v-if="existe('regularizar')">
            <router-link to="/regularizar">
                <i class="material-icons">airplay</i>
                <p>Regularizar Horas</p>
            </router-link>
        </li>
        <li v-if="existe('operador')">
            <router-link to="/operador">
                <i class="material-icons">person</i>
                <p>Operadores</p>
            </router-link>
        </li>
        <li v-if="existe('fotocheck-frame')">
            <router-link to="/fotocheck-frame">
                <i class="material-icons">person</i>
                <p>FotoCheck</p>
            </router-link>
        </li>
        <li v-if="existe('cuenta')">
            <router-link to="/cuenta">
                <i class="material-icons">person</i>
                <p>Cuentas</p>
            </router-link>
        </li>
        <li v-if="existe('planilla')">
            <router-link to="/planilla">
                <i class="material-icons">work</i>
                <p>planilla</p>
            </router-link>
        </li>
        <li v-if="existe('cargo')">
            <router-link to="/cargo">
                <i class="material-icons">work</i>
                <p>Cargo</p>
            </router-link>
        </li>
        <li v-if="existe('modulo')">
            <router-link to="/modulo">
                <i class="material-icons">work</i>
                <p>modulo</p>
            </router-link>
        </li>
        <li v-if="existe('rol')">
            <router-link to="/rol">
                <i class="material-icons">work</i>
                <p>rol</p>
            </router-link>
        </li>
        <li v-if="existe('linea')">
            <router-link to="/linea">
                <i class="material-icons">work</i>
                <p>Linea</p>
            </router-link>
        </li>
        <li v-if="existe('proceso')">
            <router-link to="/proceso">
                <i class="material-icons">work</i>
                <p>Centro Costo</p>
            </router-link>
        </li>
        <li v-if="existe('area')">
            <router-link to="/area">
                <i class="material-icons">work</i>
                <p>Actividad</p>
            </router-link>
        </li>
        <li v-if="existe('labor')">
            <router-link to="/labor">
                <i class="material-icons">work</i>
                <p>labor</p>
            </router-link>
        </li>
        <li v-if="existe('reporte-rotaciones')">
            <router-link to="/reporte-rotaciones">
                <i class="material-icons">book</i>
                <p>Reporte de Rotaciones</p>
            </router-link>
        </li>
        <li v-if="existe('reporte-semana')">
            <router-link to="/reporte-semana">
                <i class="material-icons">book</i>
                <p>Reporte por Semana</p>
            </router-link>
        </li>
        <li v-if="existe('reporte-marcas')">
            <router-link to="/reporte-marcas">
                <i class="material-icons">book</i>
                <p>Reporte de Marcas</p>
            </router-link>
        </li>
        <li v-if="existe('marcas-noche')">
            <router-link to="/marcas-noche">
                <i class="material-icons">book</i>
                <p>RPT Horas Nocturnas</p>
            </router-link>
        </li>
        <li v-if="existe('rango')">
            <router-link to="/rango">
                <i class="material-icons">book</i>
                <p>RPT Rango de Fechas</p>
            </router-link>
        </li>
        <li v-if="existe('configuracion')">
            <router-link to="/configuracion">
                <i class="material-icons">work</i>
                <p>Configuracion</p>
            </router-link>
        </li>
    </ul>
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    computed: {
        ...mapState(['modulos']),
    },
    methods: {
        existe(val){
            for (let i = 0; i < this.modulos.length; i++) {
                if (this.modulos[i]==val) {
                    return true;
                }
            }
            return false;
        }
    },
}
</script>