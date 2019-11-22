<template>
    <div id="app" :class="(statusSidebar) ? 'open':''">
        <div class="sidebar" >
            <ul>
                <li>
                    <router-link to="marcador">
                        <i class="material-icons">airplay</i>
                        <p>marcador</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="tareo">
                        <i class="material-icons">airplay</i>
                        <p>tareo</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="operador">
                        <i class="material-icons">person</i>
                        <p>Operadores</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="proceso">
                        <i class="material-icons">work</i>
                        <p>proceso</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="area">
                        <i class="material-icons">work</i>
                        <p>Area</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="labor">
                        <i class="material-icons">work</i>
                        <p>labor</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="turno">
                        <i class="material-icons">schedule</i>
                        <p>Turno</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="reporte-turno">
                        <i class="material-icons">book</i>
                        <p>Reporte por Turno</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="reporte-pendientes">
                        <i class="material-icons">book</i>
                        <p>Reporte de Pendientes</p>
                    </router-link>
                </li>
            </ul>            
        </div>
        <div class="background-sidebar" @click="closeSidebar()"></div>
        <nav class="navbar">
            <button @click="openSidebar()"></button>
        </nav>
        <div class="content">
            <slot/>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            statusSidebar: false
        }
    },
    methods: {
        url(){
            // return 
        },
        closeSidebar(){
            this.statusSidebar=false;
        },
        openSidebar(){
            this.statusSidebar=true;
        }
    },
}
</script>