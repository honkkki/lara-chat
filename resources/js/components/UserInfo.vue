<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">个人信息</div>

                    <div class="card-body" v-loading="listLoading">

                        <div class="form-group row">
                            <label for="avatar" class="col-md-4 col-form-label text-md-right">头像</label>

                            <div>
                                <el-upload
                                    class="avatar-uploader"
                                    :action="actionUrl"
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
        this.actionUrl = this.$api_url + '/user/upload/'
        this.userInfo()

    },

    methods: {
        userInfo() {
            this.listLoading = true
            axios.get(this.$api_url + '/userInfo')
                .then(res => {
                    this.imageUrl = this.$api_url + res.data.data.avatar
                    this.realUrl = res.data.data.avatar
                    this.username = res.data.data.name
                    this.listLoading = false
                })
        },

        handleAvatarSuccess(res, file) {
            this.imageUrl = URL.createObjectURL(file.raw);
            this.realUrl = res

        },

        beforeAvatarUpload(file) {
            const typeCheck = file.type === 'image/jpeg' || file.type === 'image/png';

            if (!typeCheck) {
                this.type.error('上传头像图片只能是JPG或PNG格式!');
            }

            return typeCheck;
        },

        handler() {
            if(!this.username || !this.realUrl) {
              this.$message({
                type: "error",
                message: "请填写完整信息!"
              });
              return
            }
            axios.post(this.$api_url + '/user/store', {
                avatar: this.realUrl,
                name: this.username
            }).then(
                res => {
                    if (res.data.code !== 0) {
                        this.$message({
                            type: 'error',
                            message: '提交失败',
                            duration: 1000,
                        })
                    } else {
                        this.$message({
                            type: 'success',
                            message: '提交成功',
                            duration: 1000,
                            onClose: () => {
                                // this.userInfo()
                                location.reload()
                            }
                        })
                    }
                },

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
