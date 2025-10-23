<!-- author - xdomra00 -->


<template>
    <button @click="btnClicked()" :class="!this.ready ? 'greenBack':  'redBack'" 
    :disabled="this.loading"
    :style="this.loading ? 'opacity:0.5;' :'opacity:1;'">
        <h2 class="zeroPad blackColor">
            {{!this.ready ? "Ready" : "Cancel"}}
        </h2>
    </button>
</template>


<script>
    export default {
        props: {
            ready: {
                required: true
            }
        },
        data()
        {
            return {
                loading: false,
            }
        },  

        watch: {
            ready(newval)
            {
                this.loading = false;
            },
        },

        methods: {

            btnClicked()
            {
                this.loading = true;
                this.$emit('readyPressed');
            }
        }
    }
</script>