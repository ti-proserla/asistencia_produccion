<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte Horas por Semana</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-5 col-lg-1">
                        <label for="" class="my-2"><b>Año/Semana:</b></label>
                    </div>
                    <div class="col-4 col-lg-1">
                        <input type="number" v-model="consulta.year" class="form-control">
                    </div>
                    <div class="col-3 col-lg-1">
                        <input type="number" v-model="consulta.week" class="form-control">
                    </div>
                    <div class="form-group col-5 col-lg-1">
                        <label for="" class="my-2"><b>Planilla:</b></label>
                    </div>
                    <div class="col-7 col-lg-2">
                        <select v-model="consulta.planilla_id" class="form-control">
                            <option v-for="planilla in planillas" :value="planilla.id">{{ planilla.nom_planilla }}</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-3">
                        <input type="text" class="form-control" placeholder="Busqueda por nombre" v-model="search">
                    </div>
                    <div class="col-sm-3">
                        <button @click="listar()" class="btn btn-info">
                            Buscar
                        </button>
                        <a :href="url" class="btn btn-success">Excel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre y Apellidos</th>
                                <th>Periodo</th>
                                <th>Cod. Actividad</th>
                                <th>Cod.Labor</th>
                                <th>Cod.Proceso</th>
                                <th>Dia 01</th>
                                <th>Dia 02</th>
                                <th>Dia 03</th>
                                <th>Dia 04</th>
                                <th>Dia 05</th>
                                <th>Dia 06</th>
                                <th>Dia 07</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in table.data">
                                <td>{{ item.dni }}</td>
                                <td>{{ item.NombreApellido }} </td>
                                <td>{{ item.periodo }} </td>
                                <td>{{ item.codActividad }} </td>
                                <td>{{ item.codLabor }} </td>
                                <td>{{ item.codProceso }} </td>
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
                </div>
                <div class="pagination">
                    <a v-for="n in table.last_page" :class="{active: table.current_page==n}" @click="listar(n)">{{n}}</a>
                </div>
            </div>
        </div>
        <!-- {{reporte}} -->
    </div>
</template>
<script>
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input
    },
    data() {
        return {
            reporte:[],
            planillas:[],
            consulta:{
                year: moment().format('YYYY'),
                week: moment().week()
            },
            search: '',
            table:{
                data:[]
            },
        }
    },
    computed: {
        url(){
            return url_base+'/horas-semana/'+this.consulta.year+'/'+this.consulta.week;
        }
    },
    mounted() {
        this.listarPlanilla();
    },
    methods: {
        listar(n=this.table.current_page){
            axios.get(url_base+'/reporte-semana?year='+this.consulta.year+'&week='+this.consulta.week+'&search='+this.search+'&planilla_id='+this.consulta.planilla_id+'&page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        listarPlanilla(n=this.table.from){
            axios.get(url_base+'/planilla?all=true')
            .then(response => {
                this.planillas = response.data;
            })
        },
    },
}
</script>