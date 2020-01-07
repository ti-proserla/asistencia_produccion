<template>
    <div class="row">
        <div class="col-sm-6">
            <!--Modal de pendientes-->
            <div id="modal-pendientes" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lista de Pendientes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>Nombre y Apellidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in reporte">
                                        <td>{{ item.dni }}</td>
                                        <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
             <div class="card">
                 <div class="card-header">
                    <h4 class="card-title text-center" v-if="isMovil">TAREO MOVIL <button class="btn btn-danger btn-sm btn-float-right" @click="openPendientes()">P</button></h4>
                    <h4 class="card-title text-center" v-else>TAREO <button class="btn btn-danger btn-sm btn-float-right" @click="openPendientes()">P</button></h4>
                </div>
                <div class="card-body">
                    <Select title="Turno:" v-model="tareo.turno_id">
                        <option v-for="turno in turnos" :value="turno.id">{{ turno.descripcion }}</option>
                    </Select>
                    <Select title="Linea:" v-model="tareo.linea_id">
                        <option value="">Sin Linea</option>
                        <option v-for="linea in lineas" :value="linea.id">{{ linea.nombre }}</option>
                    </Select>
                    <Select title="proceso:" v-model="tareo.proceso_id">
                        <option v-for="proceso in procesos" :value="proceso.id">{{ proceso.nom_proceso }}</option>
                    </Select>
                    <Select title="Area:" v-model="tareo.area_id">
                        <option value="">--SELECCIONAR AREA--</option>
                        <option v-for="area in areas" :value="area.id">{{ area.nom_area }}</option>
                    </Select>
                    <Select title="Labor:" v-model="tareo.labor_id">
                        <option value="">--SELECCIONAR LABOR--</option>
                        <option v-for="labor in labores" :value="labor.id">{{ labor.nom_labor }}</option>
                    </Select>
                    <form v-on:submit.prevent="guardar()">
                        <Input title="Codigo de Barras" :focusSelect='focus' type="number" v-model="tareo.codigo_barras"></Input>
                        <button type="submit" hidden></button>
                    </form>
                </div>
             </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Respuesta</h4>
                </div>
                <div class="card-body">
                    <div v-if="alert!=null" :class="'alert alert-'+alert.status" role="alert">
                        {{ alert.data }}
                    </div>
                    <div v-if="respuesta!=null && respuesta.status=='OK'"  class="fotocheck text-center" style="margin-right: auto;margin-left: auto">
                        <img :src="url(respuesta.data.foto)" alt="">
                        <p><b>{{ respuesta.data.nom_operador.split(' ')[0] }} {{ respuesta.data.ape_operador.split(' ')[0] }}</b></p>
                        <hr>
                        <h6>Jayanca Fruits</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Input from '../../dragon-desing/dg-input.vue'
import Select from '../../dragon-desing/dg-select.vue'

export default {
    components:{
        Input,
        Select
    },
    data() {
        return {
            focus: true,
            isMovil:((navigator.userAgent).search('Android')>-1),
            tareo:{
                area_id:null,
                proceso_id:null,
                labor_id:null,
                linea_id:null,
                codigo_barras:null
            },
            procesos:[],
            turnos:[],
            lineas:[],
            areas:[],
            // labores:[],
            turno_id: 0,
            area_id: 0,
            /**Estado de registro */
            respuesta: null,
            alert: null,
            /**Lista de Pendientes */
            reporte:[],
            areasLabor:[]
        }
    },
    mounted() {
        this.listarTurnos();
        this.listarProcesos();
        // this.listarAreas();
        // this.listarLabor();
        this.listarLineas();
        this.listarAreasLabor();
    },
    computed: {
        labores(){
            for (let i = 0; i < this.areas.length; i++) {
                const area = this.areas[i];
                
                if (area.id==this.tareo.area_id) {
                    this.tareo.labor_id=null;
                    console.log(area.labores);
                    return area.labores;
                }
            }
            return [];
        }
    },
    methods: {
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
        listarAreasLabor(){
            axios.get(url_base+'/area/labor')
            .then(response => {
                this.areas = response.data;
            });
        },
        // listarLabor(){
        //     axios.get(url_base+'/labor?all=true')
        //     .then(response => {
        //         this.labores = response.data;
        //         if (this.labores.length>0) {
        //             this.tareo.labor_id=this.labores[0].id;
        //         }
        //     });
        // },
        // listarAreas(){
        //     axios.get(url_base+'/area?all=true')
        //     .then(response => {
        //         this.areas = response.data;
        //         if (this.areas.length>0) {
        //             this.tareo.area_id=this.areas[0].id;
        //         }
        //     });
        // },
        listarTurnos(){
            axios.get(url_base+'/turno?all=true')
            .then(response => {
                this.turnos = response.data;
                if (this.turnos.length>0) {
                    this.tareo.turno_id=this.turnos[0].id;
                }
            });
        },
        listarLineas(){
            axios.get(url_base+'/linea?all=true')
            .then(response => {
                this.lineas = response.data;
                if (this.lineas.length>0) {
                    this.tareo.linea_id=this.lineas[0].id;
                }
            });
        },
        listarProcesos(){
            axios.get(url_base+'/proceso?all=true')
            .then(response => {
                this.procesos = response.data;
                if (this.procesos.length>0) {
                    this.tareo.proceso_id=this.procesos[0].id;
                }
            });
        },
        clearAlert(){
            setTimeout(() => {
                this.alert=null;
            }, 1000);
        },
        guardar(){
            this.$nextTick(() =>{
                if (((null==this.tareo.codigo_barras) ? '' : this.tareo.codigo_barras ).length==8) {
                    axios.post(url_base+'/tareo',this.tareo)
                    .then(response => {
                        var respuesta=response.data;
                        console.log(respuesta);
                        
                        switch (respuesta.status) {
                            case "ERROR":
                                this.alert=respuesta;
                                this.respuesta=null;
                                this.clearAlert();
                                break;
                            case "WARNING":
                                if((navigator.userAgent).search('Android')>-1){
                                    swal("", respuesta.data, 'warning');
                                }
                                this.tareo.codigo_barras=null;
                                this.alert={
                                    status: 'warning',
                                    data: respuesta.data
                                };
                                break;
                            case "OK":
                                // swal("", respuesta.data.nom_operador+" "+ respuesta.data.ape_operador, 'warning');
                                this.alert={
                                    status: 'primary',
                                    data: "TAREO: "+respuesta.data.nom_operador+" "+ respuesta.data.ape_operador
                                };
                                // this.respuesta=response.data;
                                this.tareo.codigo_barras=null;
                                break;
                            default:
                                break;
                        }
                    })
                }else{
                    this.tareo.codigo_barras=null;
                    this.respuesta={
                        status: 'ERROR',
                        data: 'Código no Valido'
                    }
                    this.clearAlert();
                }
            })
        },
        openPendientes(){
            axios.get(url_base+'/reporte-pendientes?turno_id='+this.tareo.turno_id)
            .then(response => {
                this.reporte = response.data;
            })
            $('#modal-pendientes').modal();
        }
    },
}
</script>