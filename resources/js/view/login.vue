<template>
    <div class="container">
        <div class="login">
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <img :src="url('portada.png')" alt="">
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card card-login my-5">
                        <div class="card-body">
                            <form v-on:submit.prevent="ingresar">
                                <Input title="Usuario" v-model="cuenta.usuario"></Input>
                                <Input title="Contraseña" type="password" v-model="cuenta.password"></Input>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-danger">
                                        Ingresar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Input from '../dragon-desing/dg-input.vue'

export default {
    components:{
        Input
    },
    data() {
        return {
            cuenta:{
                usuario: null,
                password: null
            }
        }
    },
    methods: {
        url(data){
            return url_base+'/../img/'+data; 
        },
        ingresar(){
            axios.post(url_base+'/login',this.cuenta)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "ERROR":
                        swal("", respuesta.data, "error");
                        break;
                    case "OK":
                        swal("", "Cuenta Iniciada.", "success");
                        this.$store.commit('auth_success', respuesta.data);
                        this.$router.push({path: "/"} );
                        break;
                    default:
                        break;
                }
            });
        }
    },
}
</script>