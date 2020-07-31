<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte Horas Nocturnas por Semana ( {{ periodoRango }} )</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-5 col-lg-4">
                        <div class="row">
                            <div class="col-4">
                                <label>Año:</label>
                                <input type="number" v-model="consulta.year" class="form-control">
                            </div>
                            <div class="col-4">
                                <label>Mes:</label>
                                <select v-model="consulta.month" class="form-control">
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="">Semana:</label>
                                <select name="" id="" v-model="periodo_inicio" class="form-control">
                                    <option :value="periodo.inicio" v-for="periodo in periodoPorAnio">{{ `${periodo.semana}` }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
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
            periodos: [],
            periodo_inicio: 0,
            periodo_fin: 0,

            reporte:[],
            planillas:[],
            fundos:[],
            consulta:{
                year: moment().format('YYYY'),
                week: moment().week(),
                month: moment().format('MM'),
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
            return url_base+'/rpt/horas_nocturnas?inicio='+this.periodo_inicio+'&fin='+this.periodo_fin+'&planilla_id='+this.consulta.planilla_id+'&turno='+this.consulta.turno+'&excel';
        },
        periodoPorAnio(){            
            return this.periodos.filter(periodo => (periodo.anio == this.consulta.year) && (moment(periodo.periodo,'YYYYMM').format('MM')==this.consulta.month) );
        },
        periodoRango(){
            var rango="";
            for (let i = 0; i < this.periodos.length; i++) {
                const element = this.periodos[i];
                if (this.periodo_inicio==element.inicio) {
                    this.periodo_fin=element.fin;
                    rango=`${this.periodo_inicio} al ${this.periodo_fin}`;
                    break;
                }
            }

            return rango;
        }
    },
    mounted() {
        this.listarPeriodos();
        this.listarPlanilla();
        this.listarFundo();
    },
    methods: {
        obtenerPeriodoActual(){
            var compareDate = moment();
            for (let i = 0; i < this.periodos.length; i++) {
                const element = this.periodos[i];
                var startDate   = moment(element.inicio);
                var endDate     = moment(element.fin).endOf('day');

                if (compareDate.isBetween(startDate, endDate)) {
                    this.periodo_inicio=element.inicio;
                    this.periodo_fin=element.fin;
                }
            }
        },
        listarPeriodos(){
            axios.get(url_base+'/nisira/periodo')
            .then(response => {
                this.periodos = response.data;
                this.obtenerPeriodoActual();
            })
        },
        listar(n=this.selectPage){
            this.selectPage=n;
            axios.get(url_base+'/rpt/horas_nocturnas?inicio='+this.periodo_inicio+'&fin='+this.periodo_fin+'&search='+this.search+'&planilla_id='+this.consulta.planilla_id+'&fundo_id='+this.consulta.fundo_id+'&turno='+this.consulta.turno+'&page='+n)
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