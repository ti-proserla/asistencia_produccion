<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Rotaciones</h4>
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-lg-3">
                        <input type="date" v-model="fecha" class="form-control">
                    </div>
                    <div class="col-lg-2">
                        <button @click="listar()" class="btn btn-info">
                            Buscar
                        </button>
                    </div>
                    <div class="col-lg-2">
                        <!-- <a :href="url" class="btn btn-success">Excel</a> -->
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Labor</th>
                                <th>Turno 01</th>
                                <th>Turno 02</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in table">
                                <td>{{ item.nom_labor }}</td>
                                <td>{{ item.turno_1 }}</td>
                                <td>{{ item.turno_2 }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" v-if="table.length==0">Sin datos</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            fecha: moment().format('YYYY-MM-DD'),
            reporte:[],
            turno_id: 0,
            turnos:[],
            table:[],
            selectPage: 1,
            search: '',
        }
    },
    mounted() {
        // this.listarTurnos();
    },
    computed: {
        url(){
            return url_base+'/rotaciones/'+this.fecha;
        }
    },
    methods: {
        listar(){
            axios.get(url_base+'/reporte-rotaciones?fecha='+this.fecha)
            .then(response => {
                this.table = response.data;
            })
        }
    },
}
</script>