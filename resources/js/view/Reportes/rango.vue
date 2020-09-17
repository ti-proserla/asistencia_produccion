<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte Rango de Fechas</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-sm-6 col-lg-3">
                        <label for="">Fecha Inicio:</label>
                        <input type="date" v-model="consulta.fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group col-sm-6 col-lg-3">
                        <label for="">Fecha Fin:</label>
                        <input type="date" v-model="consulta.fecha_fin" class="form-control">
                    </div>
                    <!-- <div class="form-group col-5 col-lg-3">
                        <label>Año/Semana:</label>
                        <div class="row">
                            <div class="col-7">
                                <input type="number" v-model="consulta.year" class="form-control">
                            </div>
                            <div class="col-5">
                                <input type="number" v-model="consulta.week" class="form-control">
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-2 form-group">
                        <label for="">Turno:</label>
                        <select v-model="consulta.turno" class="form-control">
                            <option value=null>Todos</option>
                            <option value="1">Turno 1</option>
                            <option value="2">Turno 2</option>
                        </select>    
                    </div>
                    <div class="form-group col-5 col-lg-3">
                        <label>Planilla:</label>
                        <select v-model="consulta.planilla_id" class="form-control">
                            <option v-for="planilla in planillas" :value="planilla.id">{{ planilla.nom_planilla }}</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-3">
                        <label for="">Search:</label>
                        <input type="text" class="form-control" placeholder="Busqueda por nombre o Codigo" v-model="search">
                    </div>
                    <div class="col-sm-3">
                        <button @click="listar(1)" class="btn btn-info">
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
                                <th>C.Costo</th>
                                <th>Labor</th>
                                <th>Linea</th>
                                <th>Marca 1</th>
                                <th>Marca 2</th>
                                <th>Marca 3</th>
                                <th>Marca 4</th>
                                <th>Total</th>
                                <th>Noche</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in table.data">
                                <td>{{ item.dni }}</td>
                                <td>{{ item.NombreApellido }} </td>
                                <td>{{ item.fecha_ref }} </td>
                                <td>{{ item.codActividad }} </td>
                                <td>{{ item.codLabor }} </td>
                                <td>{{ item.codProceso }} </td>
                                <td>{{ item.nom_labor }} </td>
                                <td>{{ item.linea }} </td>
                                <td>{{ item.marcas.split('@')[0]}}</td>
                                <td>{{ item.marcas.split('@')[1]}}</td>
                                <td>{{ item.marcas.split('@')[2]}}</td>
                                <td>{{ item.marcas.split('@')[3]}}</td>
                                <td>{{ item.h_trabajadas}}</td>
                                <td>{{ item.h_nocturnas}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="row">
                        <div class="col-9 text-left">
                            <h6>Pagina {{ selectPage }} de {{ table.last_page}} (TOTAL: {{table.total}})</h6>
                        </div>
                        <div class="col-3">
                            <button v-if="selectPage!=1" @click="listar(Number(selectPage)-1)"><</button>
                            <select v-model="selectPage"  v-on:change="listar()">
                                <option v-for="n in table.last_page">{{n}}</option>
                            </select>
                            <a @click="listar(Number(selectPage)+1)" v-if="!(selectPage==table.last_page||table.last_page==1)">></a>
                        </div>
                    </div>
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
            fundos:[],
            consulta:{
                fecha_inicio: moment().format('YYYY-MM-DD'),
                fecha_fin: moment().format('YYYY-MM-DD'),
                // year: moment().format('YYYY'),
                // week: moment().week(),
                planilla_id: "",
                fundo_id: "",
                turno: null
            },
            search: '',
            table:{
                data:[]
            },
            selectPage:1
        }
    },
    computed: {
        url(){
            return url_base+'/rpt/rango?fecha_inicio='+this.consulta.fecha_inicio+'&fecha_fin='+this.consulta.fecha_fin+'&planilla_id='+this.consulta.planilla_id+'&turno='+this.consulta.turno+'&excel';
        }
    },
    mounted() {
        this.listarPlanilla();
        this.listarFundo();
    },
    methods: {
        listar(n=this.selectPage){
            this.selectPage=n;
            axios.get(url_base+'/rpt/rango?fecha_inicio='+this.consulta.fecha_inicio+'&fecha_fin='+this.consulta.fecha_fin+'&search='+this.search+'&planilla_id='+this.consulta.planilla_id+'&fundo_id='+this.consulta.fundo_id+'&turno='+this.consulta.turno+'&page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        listarPlanilla(n=this.table.from){
            axios.get(url_base+'/planilla?all=true')
            .then(response => {
                this.planillas = response.data;
                this.consulta.planilla_id=this.planillas[0].id;

            })
        },
        listarFundo(){
            axios.get(url_base+'/fundo?all=true')
            .then(response => {
                this.fundos = response.data;
            })
        },
    },
}
</script>