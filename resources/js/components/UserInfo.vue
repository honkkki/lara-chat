<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">个人信息</div>

                    <div class="card-body">

                        <div class="form-group row">
                            <label for="avatar" class="col-md-4 col-form-label text-md-right">头像</label>

                            <div>
                                <el-upload
                                    class="avatar-uploader"
                                    action="http://larachat.honkki.xyz/user/upload/"
                                    :show-file-list="false"
                                    :on-success="handleAvatarSuccess"
                                    :before-upload="beforeAvatarUpload">
                                    <img v-if="imageUrl" :src="imageUrl" class="avatar">
                                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                </el-upload>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">用户名</label>
                            <div>
                                <el-input v-model="username" placeholder="请输入用户名"></el-input>
                            </div>
                        </div>

                        <div style="text-align: center">
                            <el-button type="primary" @click="handler">提交</el-button>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            username: '',
            imageUrl: '',
            actionUrl: '',
            listLoading: false,
            realUrl: '',
        }
    },

    mounted() {
        console.log('Component mounted.')

    },

    methods: {
        userInfo() {

        },

        handleAvatarSuccess(res, file) {
            this.imageUrl = URL.createObjectURL(file.raw);
            console.log(this.imageUrl)
            console.log(res)
            this.realUrl = res

        },

        beforeAvatarUpload(file) {
            // const isJPG = file.type === 'image/jpeg';
            // const isLt2M = file.size / 1024 / 1024 < 2;
            //
            // if (!isJPG) {
            //     this.$message.error('上传头像图片只能是 JPG 格式!');
            // }
            // if (!isLt2M) {
            //     this.$message.error('上传头像图片大小不能超过 2MB!');
            // }
            // return isJPG && isLt2M;

        },

        handler() {
            // if(!this.newForm.name1 || !this.newForm.level_type || this.newForm.product_category_id === '' || !this.newForm.icon || !this.newForm.weight) {
            //   this.$message({
            //     type: "error",
            //     message: "信息未填完!"
            //   });
            //   return
            // }
            axios.post('http://larachat.honkki.xyz/user/store', {
                avatar: this.realUrl,
                name: this.username
            }).then(
                res => {
                    console.log(res)
                    if (res.data.code !== 0) {
                        this.$message({
                            type: 'error',
                            message: '提交失败'
                        })
                    } else {
                        this.$message({
                            type: 'success',
                            message: '提交成功'
                        })
                    }
                }
            )
        },


    }


}
</script>

<style>
.avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.avatar-uploader .el-upload:hover {
    border-color: #409EFF;
}

.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
}

.avatar {
    width: 178px;
    height: 178px;
    display: block;
}
</style>
