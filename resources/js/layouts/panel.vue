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
                    <router-link to="regularizar">
                        <i class="material-icons">airplay</i>
                        <p>Regularizar Horas</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="operador">
                        <i class="material-icons">person</i>
                        <p>Operadores</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="planilla">
                        <i class="material-icons">work</i>
                        <p>planilla</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="linea">
                        <i class="material-icons">work</i>
                        <p>Linea</p>
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
                    <router-link to="reporte-semana">
                        <i class="material-icons">book</i>
                        <p>Reporte por Semana</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="reporte-marcas">
                        <i class="material-icons">book</i>
                        <p>Reporte de Marcas</p>
                    </router-link>
                </li>
            </ul>            
        </div>
        <div class="background-sidebar" @click="closeSidebar()"></div>
        <nav class="navbar">
            <button @click="openSidebar()" class="btn-link-success"><i class="material-icons">menu</i></button>
            
            <!-- <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Dropdown
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
            </div> -->
            <button @click="cerrar()" class="btn btn-danger btn-sm btn-float-right">Salir</button>
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
        },
        cerrar(){
            this.$store.commit('auth_close');
            this.$router.push({path: "/login"} );
        }
    },
}
</script>