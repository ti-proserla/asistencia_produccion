<template>
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>CODIGO</th>
                    <th>Nombre y Apellidos</th>
                    <th>LUNES</th>
                    <th>MARTES</th>
                    <th>MIERCOLES</th>
                    <th>JUEVES</th>
                    <th>VIERNES</th>
                    <th>SABADO</th>
                    <th>DOMINGO</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in reporte">
                    <td>{{ item.dni }}</td>
                    <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                    <td>{{ item.Lunes }}</td>
                    <td>{{ item.Martes }}</td>
                    <td>{{ item.Miercoles }}</td>
                    <td>{{ item.Jueves }}</td>
                    <td>{{ item.Viernes }}</td>
                    <td>{{ item.Sabado }}</td>
                    <td>{{ item.Domingo }}</td>
                </tr>
            </tbody>
        </table>
        <!-- {{reporte}} -->
    </div>
</template>
<script>
export default {
    data() {
        return {
            reporte:[],
        }
    },
    mounted() {
        this.listar()
    },
    methods: {
        listar(){
            axios.get(url_base+'/reporte-turno')
            .then(response => {
                this.reporte = response.data;
            })
        }
    },
}
</script>