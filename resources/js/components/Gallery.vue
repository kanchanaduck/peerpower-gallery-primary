<template>
    <div class="container">
        <div class="modal fade" tabindex="-1" role="dialog" id=exampleModal>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img v-bind:src="'upload/'+ imgModal " class="img-fluid" v-if="imgModal!=null">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><strong>Gallery</strong></div>
                    <div class="card-body">
                        <form id="file-upload-form" class="uploader">
                        <input id="file-upload" type="file" name="image" multiple @change="processFile($event)" accept="image/*" />
                            <label for="file-upload">
                                <div id="start">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div>Drop files here or click to upload..</div>
                                </div>
                            </label>
                        </form>
                        <div class="row">
                            <div class="col-4" v-for="(img, imageIndex) in imgs" :key="imageIndex" :data-index="imageIndex">
                                <div v-bind:class="[img.progressPercent==undefined || img.progressPercent==100 ? 'img-container ' : 'img-container has-progress', ''] ">
                                    <img v-bind:src="'upload/'+ img.name " v-if="img.name!=undefined">
                                    <div v-if="img.icon==true"><i class="fas fa-exclamation-triangle"></i></div>
                                    <p v-if="img.text!=''">{{ img.text }}</p>
                                    <ul class="img-manage" v-if="img.progressPercent==undefined || img.progressPercent==100"> 
                                        <li v-if="img.name!=undefined"><a class="btn btn-info" @click="openModal(img.name)" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo" v-bind:src="'upload/'+ img.name "><i class="fas fa-search"></i></a></li>
                                        <li v-if="img.name!=undefined"><a class="btn btn-danger" @click="remove(imageIndex, img.id)"><i class="far fa-trash-alt"></i></a></li>
                                        <li v-if="img.name==undefined && img.progressPercent!=100"><a class="btn btn-danger" @click="removeUnwanted(imageIndex)"><i class="far fa-trash-alt"></i></a></li>
                                    </ul>
                                    <div class="progress" v-else>
                                        <div class="progress-bar" role="progressbar" v-bind:style="'width: '+img.progressPercent+'%;'" v-bind:aria-valuenow="img.progressPercent" aria-valuemin="0" aria-valuemax="100">{{img.progressPercent}}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import fancyBox from 'vue-fancybox';
export default {
    name: 'app',
    components: {
    },
    data: function () {
        return {
            imgs: [],
            imgModal: null, 
        }
    },
    mounted() {
        this.getImage()
    },
    methods: {
        getImage: function() {
            let currentObj = this
            axios.get(
                '/lists')
                .then(function (response) {
                    currentObj.imgs = response.data
            })
        },
        processFile: function(e){
            let currentObj = this;
            for( var i = 0; i < e.target.files.length; i++ ){
                let formData = new FormData();
                let file = e.target.files[i];
                if(file.type!='image/jpeg' && file.type!='image/png'){
                    this.imgs.push({
                        text: 'File type not supported. - '+file.name,
                        icon: true,
                    })
                }
                else if(file.size>10485760){
                    this.imgs.push({
                        text: 'File size exceeded. - '+file.name,
                        icon: true,
                    })
                }
                else{
                    formData.append('files', file);
                    let index = currentObj.imgs.length
                    axios.post(
                        '/store', 
                        formData, {
                            headers: {
                                'content-type': 'multipart/form-data',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Authorization': 'Bearer '.$token,
                                'Accept': 'application/json',
                            },
                            onUploadProgress: function( progressEvent ) {
                                currentObj.$set(currentObj.imgs, index, {
                                    progressPercent: parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ))
                                })
                            }.bind(this)
                        })
                        .then(function (response) {
                            currentObj.$set(currentObj.imgs, index, response.data.files)
                        })
                        .catch(function (error) {
                            console.log(error);
                        }
                    ); 
                }
            }
        },
        openModal : function(img) {
            this.imgModal = img 
        },
        remove : function(index, id) {
            let currentObj = this;
            axios.delete(
                '/api/image/'+id,)
                .then(function (response) {
                    currentObj.$delete(currentObj.imgs, index)
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        removeUnwanted : function(index) {
            this.$delete(this.imgs, index)
        },
    },
    computed:{
        groupedImage() {
            return _.chunk(this.imgs, 3)
        },
        item_length: function () {
            return this.imgs.length;
        }
    }
}
</script>



<style scoped>
.uploader {
  display: block;
  clear: both;
  margin: 0 auto;
  width: 100%;
}
.uploader:hover{
  cursor: pointer;
}
.uploader label {
  float: left;
  clear: both;
  width: 100%;
  padding: 2rem 1.5rem;
  text-align: center;
  background: #fff;
  border-radius: 7px;
  border: 3px dashed #eee;
  -webkit-transition: all .2s ease;
  transition: all .2s ease;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}
.uploader label.hover #start i.fas {
  -webkit-transform: scale(0.8);
          transform: scale(0.8);
  opacity: 0.3;
}
.uploader #start {
  float: left;
  clear: both;
  width: 100%;
}
.uploader #start.hidden {
  display: none;
}
.uploader #start i.fas {
  font-size: 50px;
  margin-bottom: 1rem;
  -webkit-transition: all .2s ease-in-out;
  transition: all .2s ease-in-out;
}
.uploader #response {
  float: left;
  clear: both;
  width: 100%;
}
.uploader input[type="file"] {
  display: none;
}
.uploader div {
  margin: 0 0 .5rem 0;
  color: #5f6982;
}
.img-container{
  width: 100%;
  min-height: 120px;
  text-align: center;
}
.img-container.has-progress{
    padding-top: 60px;
}
.img-container i.fa-exclamation-triangle {
  font-size: 60px;
  color: #900;
}
.img-container p {
  color: #900;
}
.img-container img {
  width: 100%;
  max-height: 150px;
}
.img-container .img-manage {
  position: absolute;
  top: 50%;
  left: 42%;
  transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  list-style: none;
}
.img-container .img-manage li{
    display:inline;
}
.img-container .img-manage a {
    color: #fff;
}
.image {
    float: left;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    border: 1px solid #ebebeb;
    margin: 5px;
}

</style>
