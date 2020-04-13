<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Account</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Account</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div >
                     <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body " >
                            <div class="row " >
                                <div class="col-8 text-left " >
                                    <div v-if='page == "SHOW"' >
                                        <button v-on:click="page = 'EDIT'" type="button" class="mr-4  btn btn-primary btn-sm float-right"><i class="nav-icon fas fa-edit"></i> Edit</button>
                                        <label for="">Name</label>
                                        <p>{{this.user.name}}</p>
                                        <label for="">Email</label>
                                        <p>{{this.user.email}}</p>
                                        <label for="">Phone</label>
                                        <p>{{this.user.phone}}</p>
                                        <label for="">Address</label>
                                        <p>{{this.user.address}}</p>
                                    </div>
                                    <div  v-if='page == "EDIT"' >
                                        <button v-on:click="page = 'SHOW'" type="button" class="mr-4 btn btn-danger btn-sm float-right"><i class="nav-icon fas fa-times"></i> Cancel</button>
                                        <div class="row">
                                            <div class='col-10'>
                                                <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="form.errors.clear()">
                                                     <label for="name" >Name</label>
                                                    <input type="text" class="form-control mb-2" id="name" name="name" required autocomplete="name" autofocus placeholder="name" v-model="form.name">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
                                                    
                                                    <label for="email" >Email</label>
                                                    <input type="text" class="form-control mb-2" id="email" name="email" required autocomplete="email" autofocus placeholder="email" v-model="form.email">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></span>

                                                    <label for="phone" >Phone</label>
                                                    <input type="text" class="form-control mb-2" id="phone" name="phone" required autocomplete="phone" autofocus placeholder="phone" v-model="form.phone">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('phone')" v-text="form.errors.get('phone')"></span>

                                                    <label for="address" >Address</label>
                                                    <input type="text" class="form-control mb-2" id="address" name="address" required autocomplete="address" autofocus placeholder="address" v-model="form.address">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('address')" v-text="form.errors.get('address')"></span>

                                                    <label for="password" >Password</label>
                                                    <input type="password" class="form-control  mb-2" id="password" name="password"  autocomplete="password" autofocus placeholder="password" v-model="form.password">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></span>
                                                    
                                                    <label for="password_confirmation" >Password Confirmation</label>
                                                    <input type="password" class="form-control mb-2" id="password_confirmation" name="password_confirmation"  autocomplete="password_confirmation" autofocus placeholder="password_confirmation" v-model="form.password_confirmation">
                                                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></span>

                                                    <button type="submit" class="btn btn-success btn-sm float-right" :disabled="form.errors.any()"><i class="nav-icon fas fa-paper-plane"></i> Send</button>
                                                </form>
                                            </div>
                                        </div>
                                       
                                    </div>
                                    
                                </div> 
                                <div class="col-4" >
                                    <div class="row clearfix">
                                        <div class=" col-md-12 ">
                                            
                                            <div class="card">
                                                <div class="card-body">
                                                    <img class=" img-fluid " v-bind:src="$baseUrl + '/uploads/users/' + user.profile_pict " width="100%">
                                                    <b-button v-b-modal.modal-1 variant="primary" class="btn btn-block btn-primary btn-sm float-right"><i class="nav-icon fas fa-camera"></i> Upload</b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div> 
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.container-fluid -->
            <!-- modal -->
            <b-modal ref="modal" id="modal-1" title="Upload">
                <clipper-upload class="btn btn-primary btn-sm" v-model="imgURL">Get image</clipper-upload>
                <button class="btn btn-primary btn-sm"  @click="getResult">Crop image</button>
                <clipper-basic class="my-clipper" ref="clipper" :ratio="1" :src="imgURL">
                    <div class="placeholder" slot="placeholder">No image</div>
                </clipper-basic>
                <div>
                    <div>result:</div>
                    <img class="result" :src="resultURL" alt="">
                </div>
                <template v-slot:modal-footer>
                    <div class="w-100">
                        <button class="btn btn-primary btn-sm float-right"  @click="sendImage">Send</button>
                    </div>
                </template>
            </b-modal>
        </div>
        <!-- /.content -->

    </div>

</template>

<script>
    export default {
        data() {
            return {
                imgURL: '',
                resultURL :'',
                user: null,
                roles : [],
                page : "SHOW", // SHOW | EDIT
                form: null
            }

        },

        created() {
            this.user = this.$auth.user();
            this.form =  new Form({
                    'name'      : this.user.name,
                    'email'     : this.user.email,
                    'phone'     : this.user.phone,
                    'address'   : this.user.address,
                    'password'  : '',
                    'password_confirmation'   : '',
                } )
        },
        mounted() {
           
        },

        methods: {
            getResult: function () {
                const canvas = this.$refs.clipper.clip();//call component's clip method
                this.resultURL = canvas.toDataURL("image/jpeg", 1);//canvas->image
                // console.log( this.resultURL );
                
            },
            sendImage(){
                if( this.resultURL == '' ){
                    this.$bvToast.toast(`Crop Image Dahulu`, {
                            title: 'Error',
                            variant: 'danger',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                    return;
                }
                fetch(this.resultURL)
                    .then(res => res.blob())
                    .then( ( res ) =>{
                        // console.log( res );
                        this.submitImage( res );
                    })
            },
            submitImage( image ){
                let formData = new FormData();
                formData.append('photo', image, "imageFilename.jpeg");

                axios.post( '/auth/upload-photo',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                    ).then( response => {
                            // console.log( response.data );
                            this.$auth.user().profile_pict  = response.data.url;// + "jpeg";
                            // this.user.profile_pict = response.data.url;// + "jpeg";

                            this.$bvToast.toast(`Berhasil Ubah Akun`, {
                                title: 'Success',
                                variant: 'success',
                                autoHideDelay: 5000,
                                appendToast: true
                            });
                            this.resultURL = '';
                            this.imgURL = '';
                        }
                    )
                    .catch( error => {
                        this.$bvToast.toast(`Terjadi Kesalahan`, {
                            title: 'Error',
                            variant: 'danger',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                    });

                    this.$refs.modal.hide( );
            },
            onSubmit(){
                // console.log( this.form.data() );
                this.form
                    .post('auth/update')
                    .then( response => {
                        this.page = "SHOW" ;

                        this.$bvToast.toast(`Berhasil Ubah Akun`, {
                            title: 'Success',
                            variant: 'success',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                       
                        this.$auth.user().name      = this.form.data().name ;
                        this.$auth.user().email     = this.form.data().email ;
                        this.$auth.user().phone     = this.form.data().phone ;
                        this.$auth.user().address   = this.form.data().address ;
                        // console.log(this.$auth.user());
                    } )
                    .catch(error => {
                        this.$bvToast.toast(`Terjadi Kesalahan`, {
                            title: 'Error',
                            variant: 'danger',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                    });
            }
        }
    }

</script>
