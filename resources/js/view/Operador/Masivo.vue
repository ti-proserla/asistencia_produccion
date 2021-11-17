<template>
    <div>
        <button @click="abrirModal()"  color="btn-info">Actualización Masiva</button>

        <div id="modal-masivo" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Actualización Masiva</h5>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <!-- <v-row>
                        <v-col md=5>
                            <h5>Desde:</h5>
                            <v-row>
                                <v-col col=12> -->
                                    <input 
                                        label="Excel:" 
                                        outlined
                                        clearable
                                        dense
                                        accept=".xlsx"
                                        hide-details
                                        @change="subirMasivo"
                                        type="file"
                                    >
                                    <button @click="guardar()">Migrar</button>
                                <!-- </v-col>
                            </v-row>
                        </v-col>
                    </v-row> -->
                    <!-- <v-row v-if="datos.length>0&&empresa_id!=null&&planilla_id!=null">
                        <v-col col=12>
                            <h5>Previsualizar:</h5>
                            <v-data-table :headers="header" :items="datos"></v-data-table>
                            <div class="text-right">
                                <v-btn outlined color="primary" @click="guardar()">Guardar</v-btn>
                            </div>
                        </v-col>
                    </v-row>
                    <v-row v-if="datos_repetidos.length>0">
                        <v-col col=12>
                            <h5>Repetidos:</h5>
                            <v-data-table :headers="header" :items="datos_repetidos"></v-data-table>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col col=12 class="text-right">
                            <v-btn outlined color="secondary" @click="resetear()">Cancelar</v-btn>
                        </v-col>
                    </v-row> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .masivo{
        display: inline-block;
    }
</style>
<script>
export default {
    name: 'masivo',
    data() {
        return {
            open: false,
            alert: this.initAlert(),
            datos: [],
            datos_repetidos: [],
            header:[
                { text: 'Codigo', value: 'codigo' },
                { text: 'Nombres', value: 'nombres' },
                { text: 'Apellidos', value: 'apellidos' },
            ],
            empresa_id: null,
            planilla_id: null,
        }
    },
    mounted() {
    },
    methods: {
        abrirModal(){
            $('#modal-masivo').modal();
        },
        cerrarModal(){
            $('#modal-masivo').modal('hide');
        },
        initAlert(){
            return {
                status: '',
                visible: false,
                message: ''
            }
        },
        subirMasivo(event){
            console.log(event.target.files);
            this.datos=[];
            this.datos_repetidos=[];
            const XLSX = window.XLSX;
            // let file = this.getFile(event);
            let file = event.target.files[0];
            
            let workBook = null;
            let jsonData = null;
            if(file !== null) {
                const reader = new FileReader();
                const rABS = true;
                reader.onload = (event) => {
                    const data = event.target.result; 
                    if(rABS) {
                        workBook = XLSX.read(data, {type: 'binary'});
                        jsonData = workBook.SheetNames.reduce((initial, name) => {
                            const sheet = workBook.Sheets[name];
                            initial[name] = XLSX.utils.sheet_to_json(sheet);
                            return initial;
                        }, {});
                        console.log(jsonData);
                        const dataString = JSON.stringify(jsonData, 2, 2);
                        
                        for (var key1 in jsonData) {
                            console.log(key1);
                            this.datos=jsonData[key1];
                            break;
                        }
                    }
                }
                if(rABS) reader.readAsBinaryString(file);
                else reader.readAsArrayBuffer(file);
            }
        },
        getFile(item) {
            if(item.dataTransfer !== undefined) {
                const dt = item.dataTransfer;
                if(dt.items) {
                    if(dt.items[0].kind == 'file') {
                        return dt.items[0].getAsFile();
                    }
                }
            }
            else {
                return item[0];
            }
        },
        guardar(){
            axios.post(url_base+'/operador/masivo',{
                datos: this.datos
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("", respuesta.message, "success");
                        break;
                    // case 'ERROR':
                    //     this.alert.status="error";
                    //     this.alert.visible=true;
                    //     this.alert.message=respuesta.data;
                        // break;
                    default:
                        break;
                }
            });
        },

        guardarMasivo(){

        },
        resetear(){
            this.datos=[];
            this.datos_repetidos=[];
            this.alert=this.initAlert();
            this.open=false;
        }
    },
}
</script>