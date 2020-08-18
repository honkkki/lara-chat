<template>
    <div class="container">
        <a href="?room_id=1" class="btn btn-danger">房间1</a>
        <a href="?room_id=2" class="btn btn-primary">房间2</a>
        <hr class="divider">

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">聊天室</div>
                    <div class="panel-body">
                        <div class="messages">
                            <div class="media" v-for="message in messages">
                                <div class="media-left">
                                    <a href="#">
                                        <img class="media-object img-circle" :src="message.avatar">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <p class="time">{{message.time}}</p>
                                    <h4 class="media-heading">{{message.name}}</h4>
                                    {{message.content}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">聊天室在线用户</div>

                    <div class="panel-body">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="user in users">
                                <img :src="user.avatar" class="img-circle">
                                {{user.name}}
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <form @submit.prevent="onSubmit" @keyup.enter="onSubmit">
            <div class="form-group">
                <label for="user_id">发送给</label>

                <select class="form-control" id="user_id" v-model="user_id">
                    <option value="">所有人</option>
                    <option :value="user.id" v-for="user in users">{{user.name}}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="content">内容(回车可快速发送)</label>
                <textarea class="form-control" rows="3" id="content" v-model="content"></textarea>
            </div>

            <button type="submit" class="btn btn-dark">提交</button>
        </form>
    </div>
</template>

<script>
    let ws = new WebSocket("ws://larachat.honkki.xyz:7272");
    export default {
        data() {
            return {
                messages: [],
                content: '',
                users: [],
                user_id: '',
                //chat_users: [],

            }
        },
        created: function () {
            ws.onmessage = (e) => {
                let data = JSON.parse(e.data);
                console.log(data);

                //如果没有类型，就为空
                let type = data.type || '';
                switch (type) {
                    case 'ping':
                        ws.send('pong');
                        break;
                    case 'init':
                        axios.post('/init', {client_id: data.client_id})
                        break;
                    case 'say':
                        this.messages.push(data.data);
                        this.$nextTick(function () {
                            $('.panel-body').animate({scrollTop: $('.messages').height()});
                        })
                        break;
                    case "login":
                        this.messages.push(data.data)
                        break;
                    case 'users':
                        this.users = data.data;
                        break;
                    // case "chat_users":
                    //     this.chat_users = data.data;
                    //     break;
                    case 'history':
                        this.messages = data.data;
                        this.messages = this.messages.reverse()
                        break;

                    case 'logout':
                        this.$delete(this.users, data.client_id)
                        break;
                    default:
                        console.log(data)
                }
            }
        },
        methods: {
            onSubmit() {
                if (this.content == '') {
                    alert("请输入聊天内容")
                    return;
                }
                axios.post('/say', {content: this.content, user_id: this.user_id}).then(
                    res => {
                        if (res.data.code === 429) {
                            alert('您输入得太快了')
                        }
                    }
                )

                this.content = ''


            }
        }
    }
</script>


<style scoped>
    .panel-body {
        height: 480px;
        overflow: auto;
    }

    .media-object.img-circle {
        width: 64px;
        height: 64px;
    }
    .img-circle {
        width: 48px;
        height: 48px;
    }
    .time {
        float: right;
    }
    .media {
        margin-top: 24px;
    }
</style>
