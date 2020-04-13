<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Menu</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Menu</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body " style="height: 300px;">
                        <div class='row' >
                            <div class='col-6 ' >
                                <div class='text-left' >
                                    <button v-on:click="showAddModal()" type="button" class="mr-5  btn btn-primary btn-sm float-right"><i class="nav-icon fas fa-plus"></i></button>
                                    <label for="">Category</label>
                                </div>
                                 <div v-bind:key="index"  v-for="(menu, index) in menus" >
                                     <div class="row" >
                                         <div class="col-8 " >
                                            <button v-on:click="setSubmenus( index )" type="button" class="mb-4 btn btn-block btn-outline-primary btn-sm float-right"><i class="nav-icon fas fa-food"></i>  {{ menu.name }}  </button>
                                         </div>
                                         <div class="col-4 p-0" >
                                            <b-button-group size="sm" class="float-left " >
                                                <b-button variant="info" v-on:click="showEditModal( menu )" ><i class="nav-icon fas fa-edit"></i></b-button>
                                                <b-button variant="danger" v-on:click="showDeleteModal( menu )" ><i class="nav-icon fas fa-times"></i></b-button>
                                            </b-button-group>
                                         </div>
                                     </div>
                                </div>
                            </div>
                            <div class='col-6' >
                                <div class='text-left' >
                                    <button v-on:click="showAddSubModal(  )" type="button" class="mr-5  btn btn-primary btn-sm float-right"><i class="nav-icon fas fa-plus"></i></button>
                                    <label for="">Sub Category ( {{ menus[this.mainMenuIndex].name }} )</label>
                                </div>
                                <div v-bind:key="index"  v-for="(menu, index) in subMenus" >
                                     <div class="row" >
                                         <div class="col-8 " >
                                            <button type="button" class="mb-4 btn btn-block btn-outline-primary btn-sm float-right"><i class="nav-icon fas fa-food"></i>  {{ menu.name }}  </button>
                                         </div>
                                         <div class="col-4 p-0" >
                                            <b-button-group size="sm" class="float-left " >
                                                <b-button variant="info" v-on:click="showEditModal( menu )"  ><i class="nav-icon fas fa-edit"></i></b-button>
                                                <b-button variant="danger" v-on:click="showDeleteModal( menu )"  ><i class="nav-icon fas fa-times"></i></b-button>
                                            </b-button-group>
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
            <!-- /.container-fluid -->

        </div>
        <!-- /.content -->
        <!-- modal create -->
        <b-modal ref="modalAdd"  v-bind:title="'Add Category' ">
            <label for="name" >Name</label>
            <input type="text" class="form-control mb-2" id="name" name="name" required autocomplete="name" autofocus placeholder="name" v-model="form.name">
            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>

            <template v-slot:modal-footer>
                <div class="w-100">
                    <button class="btn btn-primary btn-sm float-right" @click="addMenu" >Add</button>
                </div>
            </template>
        </b-modal>

         <!-- modal add sub -->
        <b-modal ref="modalAddSub"  v-bind:title="'Edit' ">
            <label for="name" >Category</label>
            <p>{{menus[this.mainMenuIndex].name}}</p>
            <label for="name" >Name</label>
            <input type="text" class="form-control mb-2" id="name" name="name" required autocomplete="name" autofocus placeholder="name" v-model="form.name">
            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>

            <template v-slot:modal-footer>
                <div class="w-100">
                    <button class="btn btn-primary btn-sm float-right" @click="addMenu" >Add</button>
                </div>
            </template>
        </b-modal>

         <!-- modal edit -->
        <b-modal ref="modalEdit"  v-bind:title="'Edit' ">
            <label for="name" >Name</label>
            <input type="text" class="form-control mb-2" id="name" name="name" required autocomplete="name" autofocus placeholder="name" v-model="form.name">
            <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>

            <template v-slot:modal-footer>
                <div class="w-100">
                    <button class="btn btn-primary btn-sm float-right" @click="updateMenu" >Edit</button>
                </div>
            </template>
        </b-modal>

        <!-- modal delete  -->
        <b-modal ref="modalDelete"   v-bind:title="'Delete'">
            <label for="name" >Are You Sure ? </label>

            <template v-slot:modal-footer>
                <div class="w-100">
                    <button class="btn btn-primary btn-sm float-right" @click="deleteMenu" >Ok</button>
                </div>
            </template>
        </b-modal>
    </div>

</template>

<script>

    export default {

        data() {
            return {
                menus : [
                    {
                        'name' : ''
                    }
                ],
                selectedMenu : {},
                mainMenuIndex : 0,
                subMenus : null,
                modalData : {
                    title : '',
                    name : '',
                },
                form : new Form({
                    'name'      : '',
                    'menu_id'   : '',
                    '_method'   : '',
                } )
            }

        },

        created() {
            this.getMenus();
        },

        methods: {
            getMenus(  ){
                axios.get('/menus')
                .then(({data}) => {
                    this.menus = data;

                    if( this.mainMenuIndex == null && this.mainMenuIndex == undefined )
                        this.mainMenuIndex = 0;
                    
                    this.setSubmenus( this.mainMenuIndex );

                });
            },
            showAddModal(  ){
                this.form.reset();
                this.$refs.modalAdd.show( );
            },

            showAddSubModal(){
                this.form.reset();
                this.form.menu_id = this.menus[this.mainMenuIndex].id;
                this.$refs.modalAddSub.show( );
            },

            showEditModal( menu ){
                this.selectedMenu = menu;
                this.form.name = this.selectedMenu.name;
                this.$refs.modalEdit.show( );
            },

            showDeleteModal( menu ){
                this.selectedMenu = menu;
                this.$refs.modalDelete.show( );
            },

            setSubmenus( index ){
                this.mainMenuIndex = index;
                this.subMenus = this.menus[this.mainMenuIndex].menus;
            },
            addMenu(){
                // this.form.password_confirmation = this.form.password; // Temp for this form only.
                this.form._method = 'POST';
                this.form
                    .post('/menus')
                    .then( success => {
                        this.getMenus();
                        this.$refs.modalAdd.hide( );
                        this.$refs.modalAddSub.hide( );
                    });
            },
            deleteMenu(  ){
                this.form._method = 'DELETE';
                
                this.form
                    .post('/menus/'+ this.selectedMenu.id )
                    .then( success => {
                        this.getMenus();
                        this.$refs.modalDelete.hide( );
                    });
            },
            updateMenu(  ){
                this.form._method = 'PUT';
                
                this.form
                    .post('/menus/'+ this.selectedMenu.id )
                    .then( success => {
                        this.getMenus();
                        this.$refs.modalEdit.hide( );
                    });
            }
        }
    }

</script>
