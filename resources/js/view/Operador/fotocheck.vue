<template>
    <div class="container-fluid">
        <div class="oculto-impresion">
            <div class="row">
                <div class="col-sm-12">
                    <label for="">Seleccionar Personal</label>
                    <v-select multiple label="dni" :options="opt" @search="getOperadores" :value="hola" @input="setSelected" :filterable="false" class="mb-3">
                        <template slot="option" slot-scope="option">
                            <div class="v-select-options d-center">
                                {{ option.dni+' | '+option.nom_operador+" "+option.ape_operador }}
                            </div>
                        </template>
                        <template slot="selected-option" slot-scope="option">
                            <div class="selected d-center v-select-options">
                                {{ option.dni+' | '+option.nom_operador+" "+option.ape_operador }}
                            </div>
                        </template>
                        <template slot="no-options">
                            Filtrar datos
                        </template>
                    </v-select>
                </div>
                <div class="col-sm-4">
                    <button class=" btn btn-info" @click="filtrar()">Filtrar</button>
                    <button class=" btn btn-primary" @click="imprimir()">Imprimir</button>
                </div>
                <div class="col-sm-8 mt-2 text-right">
                    Tipo de Fotocheck: 
                    <input type="radio" value="0" v-model="formato">
                    Vertical
                    <input type="radio" value="1" v-model="formato">
                    horizontal
                </div>
            </div>
        </div>
        <div class="fotocheck-list" v-if="formato==0">
            <div v-for="operador in operadores" class="fotocheck text-center">
                <div>
                    <img :src="url_logo()" class="logo" alt="">
                </div>
                <img class="foto my-2" :src="url(operador.foto)" alt="">
                <h5>{{ operador.nom_operador }}</h5>
                <h5>{{ operador.ape_operador }}</h5>
                <div class="franja">
                    <h6 class="my-0" v-if="operador.cargo!=null">{{ operador.cargo.nom_cargo }}</h6>
                    <h6 v-else></h6>
                </div>
                <div class="my-1">
                    <barcode :value="operador.dni" height="38" width="2" fontSize="14"></barcode>
                </div>
            </div>
        </div>
        <div class="fotocheck-list" v-if="formato==1">
            <div v-for="operador in operadores" class="fotocheck2">
                <div class="franja-vertical"></div>
                <div class="portada">
                    <div class="row">
                        <div class="col-6">
                            <img :src="url_logo()" class="logo" alt="">
                        </div>
                        <div class="col-6 text-right">
                            <img :src="url_costa()" class="logo" alt="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <img v-if="operador.foto!=null" class="foto" :src="url(operador.foto)" alt="">
                        </div>
                        <div class="col-7">
                            <div class="datos">
                                <h3 class="mb-1">{{ operador.ape_operador }}</h3>
                                <h4 class="mb-2">{{ operador.nom_operador }}</h4>
                                <h5 class="mb-1">N° DOCUMENTO: {{ operador.dni }}</h5>
                            </div>
                            <barcode :value="operador.dni" height="30" :width="2.3" fontSize="14" textAlign="right"></barcode>
                        </div>
                    </div>
                </div>
                <div class="franja-vertical"></div>
                <div class="contra-portada">
                    <div class="normas mb-2">
                        <ul>
                            <li>Este Fotochek es de uso obligatorio para su identificación y registro de asistencia.</li>
                            <li>Para el <b>pago de sus haberes</b> se considerará las marcaciones realizadas con este fotochek.</li>
                            <li>Este fotochek es personal e intrasferible, no otorga ningún derecho legal de representación de la Empresa.</li>
                            <li>Es caso de pérdida comunicarse con el área de Recursos Humanos.</li>
                            <li>Evite accidentes de trabajo, cumpla con las normas de seguridad.</li>
                            <li>Cuide el medio ambiente, deposite cada residuo en su lugar.</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <barcode :value="operador.dni" height="55" :width="4.3" fontSize="14"></barcode>
                    </div>
                </div>
                <div class="franja-horizontal">
                </div>
                <div class="franja-horizontal">
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .fotocheck2 .franja-vertical{
        background-color: #00b050;
    }
    .franja-horizontal{
        background-color: #1f497d;
    }
</style>
<script>
import VueBarcode from 'vue-barcode';
export default {
    components:{
        'barcode': VueBarcode 
    },
    data() {
        return {
            operadores: [],
            id: this.$route.params.id,
            opt:[],
            hola:[],
            planillas:[],
            planilla_id: null,
            formato: 0
        }
    },
    mounted() {
        this.listarPlanilla();
        axios.get(url_base+'/operador/unitarios?ids='+1)
        .then(response => {
            this.operadores = response.data;
        });
    },
    methods: {
        filtrar(){
            var ids="";
            for (let i = 0; i < this.hola.length; i++) {
                const element = this.hola[i].id;
                if((this.hola.length-1)==i){
                    ids=ids+element;
                }else{
                    ids=ids+element+'-';
                }
            }
            axios.get(url_base+'/operador/unitarios?ids='+ids)
            .then(response => {
                this.operadores = response.data;
            });
            // axios.get(url_base+'/operador/'+this.id)
            // .then(response => {
            //     this.operador = response.data;
            // });
        },
        setSelected(value){
            this.hola=value  
        },
        listarPlanilla(){
            axios.get(url_base+'/planilla?all=true')
            .then(response => {
                this.planillas = response.data;
                this.planilla_id=this.planillas[0].id;
            })
        },
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
        url_logo(){
            return url_base+'/../storage/logotipo.png'
        },
        url_costa(){
            return url_base+'/../costa.jpg'
        },
        imprimir() {
            window.print();
        },
        getOperadores(search,loading){
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {
                vm.opt=[]
                if (search!="") {
                    axios.get(url_base+'/operador?all=true&search='+search)
                    .then(response => {
                        var respuesta=response.data;
                        vm.opt=respuesta;
                        loading(false);
                    })
                }else{
                    loading(false);
                }
            }, 350)
        ,
    },
}
</script>