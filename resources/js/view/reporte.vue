<template>
    <div class="container">
        <h3 class="my-3">REPORTE DE CONTEO</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-5 form-group">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="">Fecha Inicio:</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="datetime-local" v-model="fecha_inicio" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-5 form-group">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="">Fecha Fin</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="datetime-local" v-model="fecha_fin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 form-group">
                        <button @click="listar" class="btn btn-info">
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header text-center">
                        TOTAL
                    </div>
                    <div class="card-body text-center">
                        <h4 v-if="conteos!=null">{{ totales }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-lg-9">
                <div class="card">
                    <div class="card-header">
                        CAJAS POR LINEA
                    </div>
                    <div class="card-body">
                        <line-chart :chart-data="datacollection" :options="options" class="chart-data"></line-chart>
                    </div>
                </div>
            </div>
        </div>
        <!-- <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Fecha y Hora</th>
                    <th>COD PRODUCTO</th>
                    <th>COD LINEA</th>
                    <th>COD BARRAS</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(conteo,index) in conteos">
                    <td>{{ index+1 }}</td>
                    <td>{{ conteo.created_at }}</td>
                    <td>{{ conteo.cod_producto }}</td>
                    <td>{{ conteo.cod_linea }}</td>
                    <td>{{ conteo.cod_barras }}</td>
                </tr>
            </tbody>
        </table> -->
    </div>
</template>
<style>
  .chart-data{
      max-height: 250px;
  }
</style>
<script>
import LineChart from './LineChart.js'
export default {
    components:{
        LineChart
    },
    data() {
        return {
            conteos:null,
            fecha_inicio: moment().format('YYYY-MM-DDT00:00'),
            fecha_fin: moment().format('YYYY-MM-DDTHH:mm'),
            datacollection: null,
            options: {
                responsive: true,
                maintainAspectRatio:false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                }
            }
        }
    },
    mounted() {
        this.listar();
    },
    computed: {
        totales(){
            var total=0;
            for (let i = 0; i < this.conteos.length; i++) {
                const element = this.conteos[i];
                total+=Number(element.count);
            }
            return total;
        }
    },
    methods: {
        listar(){
            axios
            .get(url_base+'/conteo?fi='+this.fecha_inicio+'&ff='+this.fecha_fin)
            .then(response => {
                this.conteos = response.data;
                this.fillData();
            })
        },
        fillData () {
            var datasets=[];
            var labels=[];
            for (let i = 0; i < this.conteos.length; i++) {
                const element = this.conteos[i];
                labels.push('Linea '+element.cod_linea);
                datasets.push(element.count);
            }
            this.datacollection = {
                labels: labels,
                datasets: [{
                    data: datasets,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255,99,132,1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }],
            }
        },
        getRandomInt () {
            return Math.floor(Math.random() * (50 - 5 + 1)) + 5
        }
    },
}
</script>