<template>
    <div>
        <button @click="print()" class="oculto-impresion btn btn-primary">Imprimir</button>
        <div style="width: 450px;" class="text-center">
            <h4>{{ operador.nom_ape }}</h4>
            <barcode :value="operador.dni" height="55" :width="5" fontSize="14"></barcode>
        </div>
    </div>
</template>

<script>
import VueBarcode from 'vue-barcode';

export default {
    components:{
        'barcode': VueBarcode 
    },
    data() {
        return {
            operador:{
                dni: '',
                nom_ape: ''
            }    
        }
    },
    mounted() {
        this.operador.dni=this.$route.params.codigo;
        this.operador.nom_ape=this.$route.params.nom_ape;
        this.print();
    },
    methods: {
        print(){
            window.print();
        }
    },    
}
</script>