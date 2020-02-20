<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Marcas</h4>
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-sm-6 col-lg-3 form-group">
                        <label for="">Día:</label>
                        <input type="date" v-model="fecha" class="form-control">
                    </div>
                    <div class="col-lg-2 form-group">
                        <label for="">Turno:</label>
                        <select v-model="turno" class="form-control">
                            <option value="1">Turno 1</option>
                            <option value="2">Turno 2</option>
                        </select>    
                    </div>
                    <div class="form-group col-5 col-lg-3">
                        <label for="" >Planilla:</label>
                        <select v-model="planilla_id" class="form-control">
                            <option v-for="planilla in planillas" :value="planilla.id">{{ planilla.nom_planilla }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 form-group">
                        <label>Nombre:</label>
                        <input type="text" class="form-control" placeholder="Busqueda por nombre" v-model="search">
                    </div>
                    <div class="col-sm-6 col-lg-2 form-group">
                        <button @click="listar(1)" class="btn btn-info mt-3">
                            Buscar
                        </button>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <a :href="url" class="btn btn-success mt-3">Excel</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre y Apellidos</th>
                                <th>Marca 1</th>
                                <th>Marca 2</th>
                                <th>Marca 3</th>
                                <th>Marca 4</th>
                                <th>Marca 5</th>
                                <th>Marca 6</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in table.data">
                                <td>{{ item.dni }}</td>
                                <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                                <td>{{ item.marcas.split('@')[0]}}</td>
                                <td>{{ item.marcas.split('@')[1]}}</td>
                                <td>{{ item.marcas.split('@')[2]}}</td>
                                <td>{{ item.marcas.split('@')[3]}}</td>
                                <td>{{ item.marcas.split('@')[4]}}</td>
                                <td>{{ item.marcas.split('@')[5]}}</td>
                                <td>{{ item.total}}</td>
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
            table:{
                data:[]
            },
            selectPage: 1,
            search: '',
            turno: 1,
            planillas:[],
            planilla_id: null
        }
    },
    mounted() {
        this.listarTurnos();
        this.listarPlanilla();
    },
    computed: {
        url(){
            return url_base+'/marcas-tuno/'+this.fecha+'/'+this.turno;
        }
    
    },
    methods: {
        listarPlanilla(n=this.table.from){
            axios.get(url_base+'/planilla?all=true')
            .then(response => {
                this.planillas = response.data;
                this.planilla_id=this.planillas[0].id;

            })
        },
        listarTurnos(){
            axios.get(url_base+'/turno?all=true')
            .then(response => {
                this.turnos = response.data;
                if (this.turnos.length>0) {
                    this.turno_id=this.turnos[0].id;
                }
            });
        },
        listar(n=this.selectPage){
            this.selectPage=n;
            axios.get(url_base+'/reporte-marcas?turno='+this.turno+'&fecha='+this.fecha+'&search='+this.search+'&page='+n)
            .then(response => {
                this.table = response.data;
            })
        }
    },
}
</script>