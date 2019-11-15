<template>
    <div id="app">
        <div class="sidebar">
            <ul>
                <li>
                    <router-link to="marcador">
                        <i class="material-icons">airplay</i>
                        <p>marcador</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="operador">
                        <i class="material-icons">person</i>
                        <p>Operadores</p>
                    </router-link>
                </li>
                <li>
                    <router-link to="actividad">
                        <i class="material-icons">work</i>
                        <p>Actividad</p>
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
            </ul>            
        </div>
        <div class="content">
            <slot/>
        </div>
    </div>
</template>
<script>
export default {
    
}
</script>