<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Pendientes</h4>
            </div>
            <div class="card-body">
                 <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombre y Apellidos</th>
                            <th>Ver Marcas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in reporte">
                            <td>{{ item.dni }}</td>
                            <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                            <td>{{ item.descripcion }}</td>
                            <td><button @click="consultarMarcas(item.operador_id,item.turno_id)">Ver Marcas</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            reporte:[],
            marcas:[]
        }
    },
    mounted() {
        this.listar();
    },
    methods: {
        listar(){
            axios.get(url_base+'/reporte/pendientes-regularizar')
            .then(response => {
                this.reporte = response.data;
            })
        },
        consultarMarcas(operador_id,turno_id){
            axios.get(url_base+'/marcador?operador_id='+operador_id+'&turno_id='+turno_id)
            .then(response => {
                this.marcas = response.data;
            })
        }
    },
}
</script>