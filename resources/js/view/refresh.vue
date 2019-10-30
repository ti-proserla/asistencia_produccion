<template>
    <div class="container">
        <h3 class="my-3">REPORTE DE CONTEO</h3>
        <div class="card">
            <div class="card-header">
                CAJAS POR LINEA
            </div>
            <div class="card-body">
                <div class="card-body">
                    <line-chart :chart-data="datacollection" :options="options" class="chart-data"></line-chart>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LineChart from './LineChart.js'
export default {
    components:{
        LineChart
    },data() {
        return {
            conteos:null,
            fecha_inicio: moment().format('YYYY-MM-DDT00:00'),
            fecha_fin: moment().format('YYYY-MM-DDT23:59'),
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
        this.refresh()
    },
    methods: {
        refresh(){
            this.listar();
            var t=this;
            setTimeout(() => {
                t.refresh();
            }, 5000);
        },
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
    }
}
</script>