<template>
    <div class="row">
        <div class="col-sm-6">
             <div class="card">
                 <div class="card-header">
                    <h4 class="card-title">Registro de Marcación</h4>
                </div>
                <div class="card-body">
                    <Select title="Turno:" v-model="tareo.turno_id">
                        <option v-for="turno in turnos" :value="turno.id">{{ turno.descripcion }}</option>
                    </Select>
                    <Select title="proceso:" v-model="tareo.proceso_id">
                        <option v-for="proceso in procesos" :value="proceso.id">{{ proceso.nom_proceso }}</option>
                    </Select>
                    <Select title="Area:" v-model="tareo.area_id">
                        <option v-for="area in areas" :value="area.id">{{ area.nom_area }}</option>
                    </Select>
                    <Select title="Labor:" v-model="tareo.labor_id">
                        <option v-for="labor in labores" :value="labor.id">{{ labor.nom_labor }}</option>
                    </Select>
                    <form v-on:submit.prevent="guardar()">
                        <Input title="Codigo de Barras" focusSelect="true" v-model="tareo.codigo_barras"></Input>
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
                <div v-if="respuesta!=null" class="card-body">
                    <div v-if="respuesta.status=='OK'" class="alert alert-success" role="alert">
                        Marcado Correcto
                    </div>
                    <div v-else class="alert alert-danger" role="alert">
                        {{ respuesta.data }}
                    </div>
                    <div v-if="respuesta.status=='OK'"  class="fotocheck text-center" style="margin-right: auto;margin-left: auto">
                        <img src="https://cdn1.iconfinder.com/data/icons/user-avatars-2/300/10-512.png" alt="">
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
            tareo:{
                area_id:null,
                proceso_id:null,
                labor_id:null,
                codigo_barras:null
            },
            procesos:[],
            turnos:[],
            areas:[],
            labores:[],
            respuesta: null,
            turno_id: 0,
            area_id: 0,
        }
    },
    mounted() {
        this.listarTurnos();
        this.listarProcesos();
        this.listarAreas();
        this.listarLabor();
    },
    methods: {
        listarLabor(){
            axios.get(url_base+'/labor?all=true')
            .then(response => {
                this.labores = response.data;
                if (this.labores.length>0) {
                    this.tareo.labor_id=this.labores[0].id;
                }
            });
        },
        listarAreas(){
            axios.get(url_base+'/area?all=true')
            .then(response => {
                this.areas = response.data;
                if (this.areas.length>0) {
                    this.tareo.area_id=this.areas[0].id;
                }
            });
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
        listarProcesos(){
            axios.get(url_base+'/proceso?all=true')
            .then(response => {
                this.procesos = response.data;
                if (this.procesos.length>0) {
                    this.proceso_id=this.procesos[0].id;
                }
            });
        },
        guardar(){
            this.$nextTick(() =>{
                if (((null==this.tareo.codigo_barras) ? '' : this.tareo.codigo_barras ).length==8) {
                    axios.post(url_base+'/tareo',this.tareo)
                    .then(response => {
                        this.respuesta=response.data;
                        this.tareo.codigo_barras=null;
                    })
                }else{
                    this.tareo.codigo_barras=null;
                    this.respuesta={
                        status: 'ERROR',
                        data: 'Código no Valido'
                    }
                }
            })
        }
    },
}
</script>