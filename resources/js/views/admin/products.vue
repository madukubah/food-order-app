<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Products</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="card" v-if=" selectedItem == null && selectedItem == undefined " >
                    <div class="card-header">
                        <h3 class="card-title">List</h3>

                        <div class="card-tools">
                            <div class="input-group input-group-sm" >
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    <button v-on:click="page = 'CREATE'" type="button" class="btn btn-block btn-primary btn-sm">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0" style="min-height: 300px;">
                        <b-table :items="items"  :busy="false" striped :hover="true" :small="false" :fields="fields" outlined>
                            <template v-slot:cell(menu)="data">
                                {{ data.item.menu.name }}
                            </template>

                            <template v-slot:cell(action)="data">
                                <button v-on:click="showdetail( data.item )" type="button" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-eye"></i></button>
                            </template>

                            <template v-slot:cell(price)="data">
                                {{ data.item.unit }} {{ data.item.price }}
                            </template>
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>Loading...</strong>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
                <div class="card" v-else >
                    <div class="card-body" >
                        <div class="row " >
                            <div class="col-8 text-left " >
                                <button v-on:click=" back(  )" type="button" class="mb-2 btn btn-danger btn-sm "><i class="nav-icon fas fa-arrow-left"></i> Back</button>
                            </div>
                        </div>
                        <div class="row " >
                            <div class="col-6 text-left " >
                                <label for="">Code</label>
                                <p>{{this.selectedItem.code}}</p>
                                <label for="">Menu</label>
                                <p>{{this.selectedItem.menu.name}}</p>
                                <label for="">Name</label> <i @click="changeName()" class="nav-icon fas fa-edit"></i> 
                                <p v-if="name" >{{this.selectedItem.name}}</p>
                                <form  @keydown="form.errors.clear()" v-else  action="" @submit.prevent="updateProduct" >
                                    <div class="mb-2 input-group input-group-sm" >
                                            <input type="text" class="form-control" id="name" name="name" required autocomplete="name" autofocus placeholder="Name" v-model="form.name">
                                            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
                                    </div>
                                </form>

                                <label for="">Description</label> <i @click="changeDescription()" class="nav-icon fas fa-edit"></i> 
                                <p v-if="description" >{{this.selectedItem.description}}</p>
                                <form  @keydown="form.errors.clear()" v-else  action="" @submit.prevent="updateProduct" >
                                    <div class="mb-2 input-group input-group-sm" >
                                            <input type="text" class="form-control" id="description" name="description" required autocomplete="description" autofocus placeholder="Description" v-model="form.description">
                                            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('description')" v-text="form.errors.get('description')"></span>
                                    </div>
                                </form>

                                <label for="">Price</label> <i @click="changePrice()" class="nav-icon fas fa-edit"></i> 
                                <p v-if="price" >{{this.selectedItem.unit}} {{this.selectedItem.price}}</p>
                                <form  @keydown="form.errors.clear()" v-else  action="" @submit.prevent="updateProduct" >
                                    <div class="mb-2 input-group input-group-sm" >
                                            <input type="text" class="form-control" id="price" name="price" required autocomplete="price" autofocus placeholder="Price" v-model="form.price">
                                            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('price')" v-text="form.errors.get('price')"></span>
                                    </div>
                                </form>

                            </div>
                            <div class="col-4 text-left " >
                                <img class=" img-fluid " v-bind:src="$baseUrl + '/uploads/products/' + imageUrl" width="100%">
                            </div>
                            <div class="col-2 text-left " >
                                <div v-bind:key="index"  v-for="(image, index) in this.selectedItem.images" >
                                    <div  style="
                                        display: inline-block;
                                        position: relative;
                                    ">
                                        <img @click="selectImage(image)" v-bind:src="$baseUrl + '/uploads/products/' + image.url " width="100%" class="p-1 img-fluid "> 
                                        <div class="float-right" style="
                                        position: absolute;
                                        top: 0px;right: 0px;
                                        padding-right: 8px;
                                        padding-top: 4px;
                                        display: inline-block;">
                                            <i @click="showDeleteImage(image)" class="text-danger nav-icon fas fa-times"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /.content -->
        <!-- modal delete  -->
        <b-modal ref="modalDeleteImage"   v-bind:title="'Delete'">
            <label for="name" >Are You Sure ? </label>

            <template v-slot:modal-footer>
                <div class="w-100">
                    <button class="btn btn-primary btn-sm float-right" @click="deleteImage" >Ok</button>
                </div>
            </template>
        </b-modal>
    </div>

</template>

<script>

    export default {

        data() {
            return {
                isBusy: false,
                name: true,
                description: true,
                price: true,
                form: new Form({
                    'name': '',
                    'description': '',
                    'price': '',
                    'menu_id': '',
                    '_method': '',
                }),
                selectedItem:  null,
                selectedItemImage:  null,
                imageUrl:  'default.jpg',
                page : "INDEX", // INDEX | DETAIL | EDIT | CREATE
                fields: [ 'code', "menu", 'name', "description", "price", 'action'],
                items: [],
            }
        },

        mounted() {
            this.getProducts(  );
        },

        methods: {
            selectImage(image){
                this.imageUrl = image.url;
            },
            showDeleteImage(image){
                // console.log(image);
                this.selectedItemImage = image;
                this.$refs.modalDeleteImage.show( );
            },
            deleteImage(){
                var _form = new Form({
                    '_method': 'DELETE',
                });
                
                _form
                    .post('delete-image/' + this.selectedItemImage.id )
                    .then( response => {
                        this.$bvToast.toast(`Berhasil Hapus Gambar`, {
                            title: 'Success',
                            variant: 'success',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                        this.getDetail( this.selectedItem.id );
                        this.selectedItemImage = null;
                    } )
                    .catch(error => {
                        this.$bvToast.toast(`Terjadi Kesalahan`, {
                            title: 'Error',
                            variant: 'danger',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                    });
                this.$refs.modalDeleteImage.hide( );
            },
            getProducts( page = 1 ){
                this.isBusy = true;
                axios.get('/products?page='+ page )
                .then(({data}) => {
                    this.items = data.data;
                    this.isBusy = false;
                });
            },
            getDetail( id ){
                var ind = this.items.findIndex( elm => elm == this.selectedItem );
                axios.get('/products/' + id )
                .then(({data}) => {
                    this.selectedItem = data;
                    this.items[ ind ] = this.selectedItem;
                    if( this.selectedItem.images.length > 0 )
                        this.imageUrl = this.selectedItem.images[0].url;
                    else
                        this.imageUrl = 'default.jpg';
                });
            },
            changeName(){
                this.name = ! this.name;
            },
            changeDescription(){
                this.description = ! this.description;
            },
            changePrice(){
                this.price = ! this.price;
            },
            updateProduct(){
                this.form._method = 'PUT';
                this.form
                    .post('products/' + this.selectedItem.id )
                    .then( response => {

                        this.$bvToast.toast(`Berhasil Ubah Data`, {
                            title: 'Success',
                            variant: 'success',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                        this.getDetail( this.selectedItem.id );
                       
                        this.name = true;
                        this.description = true;
                        this.price = true;
                    } )
                    .catch(error => {
                        this.$bvToast.toast(`Terjadi Kesalahan`, {
                            title: 'Error',
                            variant: 'danger',
                            autoHideDelay: 5000,
                            appendToast: true
                        });
                    });
            },
            showdetail( item ){
                this.selectedItem = item;
                if( this.selectedItem.images.length > 0 )
                    this.imageUrl = this.selectedItem.images[0].url;

                this.form.name = this.selectedItem.name;
                this.form.description = this.selectedItem.description;
                this.form.price = this.selectedItem.price;
                this.form.menu_id = this.selectedItem.menu_id;
            },
            back(  ){
                this.selectedItem = null;
                this.name = true;
                this.description = true;
                this.price = true;
            },
        }
    }

</script>
<style>
    label.cabinet{
	display: block;
	cursor: pointer;
}

figure figcaption {
    position: fixed;
    bottom: 0;
    color: #000;
    width: 100%;
    padding-left: 9px;
    padding-bottom: 5px;
    text-shadow: 0 0 10px #000;
}
</style>