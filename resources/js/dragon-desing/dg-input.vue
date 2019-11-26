<template>
    <div class="form-group">
        <div class="form-group-dragon" :class="{'focus': focus, 'active' : withContent }">
            <div class="form-group-content">
                <label for="">{{ title }}</label>
                <input :readonly="readonly||readonlyFocusInit" @focus="OpenFocus" @blur="exitFocus" :type="type" ref="text" :value="value" @input="updateFormControl">
            </div>
        </div>
        <strong class="form-dragon-error" v-if="error!=null">{{ error }}</strong>
    </div>
</template>
<script>
export default {
    name: "dg-input",
    props:['type','title','pName','pId','value','error','readonly','focusSelect'],
    data() {
        return {
            focus: false,
            readonlyFocusInit: false
        }
    },
    mounted() {
        if (this.focusSelect){
            this.$refs.text.focus();
        }
    },
    computed: {
        withContent(){
            if (this.type=="date"){
                return true
            }else{
                if (this.value==null) {
                    return false;
                }
                if (this.value.length==0) {
                    return false;
                }else{
                    return true;
                }
            }
        }
    },
    methods: {
        OpenFocus(){
            if (this.focusSelect){
                this.readonlyFocusInit=true;
                setTimeout(() => {
                    this.readonlyFocusInit=false;
                },300 );
            }
            this.focus=true;
        },
        exitFocus(){
            this.focus=false;
        },
        updateFormControl(){
            this.$emit('input',this.$refs.text.value);
        }
    },
}
</script>