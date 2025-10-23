<!-- author - xdomra00 -->

<template>
    <div>
        <h3 style="padding: 0;">Party code</h3>
        <h1 style="padding: 0;">{{ code }}</h1>
    </div>
</template>


<script>
import ReadyButtonComponent from './ReadyButtonComponent.vue';


export default {
        props: ['code', 'ready'],
        data()
        {
            return {
            }
        },
    
        methods: {
        }
    }


</script>


<style>


</style>