<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><strong>Disk Usage</strong></div>
                    <div class="card-body pl-5">
                        <div class="row">
                            <div class="col-sm-3">Total Size:</div>
                            <div class="col-sm-9">{{ (total/Math.pow(1024,2)).toFixed(2) }}MB ({{total}}B)</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">No of files:</div>
                            <div class="col-sm-9">{{ count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-3">
                    <div class="card-header"><strong>File Usage Compositions</strong></div>
                    <div class="card-body">
                        <div  v-if="files.length  > 0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>No of files</th>
                                        <th>Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="file in files" :key="file.id">
                                        <td>{{ file.type }}</td>
                                        <td>{{ file.count }}</td>
                                        <td>{{ (file.size/Math.pow(1024,2)).toFixed(2) }}MB ({{file.size}}B)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            No data
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getImage()   
        },
        data: function () {
            return {
                files:[]
            }
        },
        methods: {
            getImage: function() {
                let currentObj = this
                axios
                    .get('/group')
                    .then(function (response) {
                        currentObj.files = response.data
                })
            },
        },
        computed:{
            total: function(){
                return this.files.reduce(function(total, item){
                    return total + item.size; 
                },0);
            },
            count: function(){
                return this.files.reduce(function(total, item){
                    return total + item.count ; 
                },0);
            },
            bytesToSize: function(bytes) {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            }
        }
    }
</script>
