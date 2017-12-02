<template>
  <div id="app">
    <vue-particles
      color="#ffffff"
      :particleOpacity="0.5"
      :particlesNumber="100"
      shapeType="circle"
      :particleSize="10"
      linesColor="#ffffff"
      :linesWidth="1"
      :lineLinked="true"
      :lineOpacity="0.3"
      :linesDistance="150"
      :moveSpeed="1"
      :hoverEffect="false"
      :clickEffect="false"/>
    <el-card class="card">
      <header>ZeroNote</header>
      <el-tabs value="login">
        <el-tab-pane label="Login" name="login">
          <el-input v-model="email" placeholder="E-mail" :disabled="loading"></el-input>
          <el-input v-model="password" type="password" placeholder="Password" :disabled="loading"></el-input>
          <el-button type="primary" :loading="loading" v-on:click="loginClick">Login</el-button>
        </el-tab-pane>
        <el-tab-pane label="Register" name="register">
          <el-input v-model="email" placeholder="E-mail" :disabled="loading"></el-input>
          <el-input v-model="password" type="password" placeholder="Password" :disabled="loading"></el-input>
          <el-input v-model="password2" type="password" placeholder="Re-type password" :disabled="loading"></el-input>
          <el-input v-model="nickname" placeholder="Nickname (Optional)" :disabled="loading"></el-input>
          <el-button type="primary" :loading="loading" v-on:click="regClick">Register</el-button>
        </el-tab-pane>
      </el-tabs>
      <footer>&copy; 2017 DeepAQ.</footer>
    </el-card>
  </div>
</template>

<script>
import api from './utils/api'
export default {
  data () {
    return {
      email: '',
      password: '',
      password2: '',
      nickname: '',
      loading: false
    }
  },
  methods: {
    showError (msg) {
      this.$message({
        showClose: true,
        type: 'error',
        message: msg
      })
    },
    loginClick () {
      if (this.email == '' || this.password == '') {
        this.showError(`Please type your E-mail and password`)
        return
      }
      this.loading = true
      api('auth/login', {
        email: this.email,
        password: this.password
      }).then(data => {
        localStorage.token = data.token
        localStorage.nickname = data.nickname
        window.location = '/'
      }).catch(reason => {
        this.showError(`Login failed: ${reason}`)
      }).then(() => {
        this.loading = false
      })
    },
    regClick () {
      if (this.email == '' || this.password == '') {
        this.showError(`Please type your E-mail and password`)
        return
      }
      if (this.password != this.password2) {
        this.showError(`Passwords do not match, please try again`)
        return
      }
      this.loading = true
      api('auth/reg', {
        email: this.email,
        password: this.password,
        nickname: this.nickname
      }).then(data => {
        localStorage.token = data.token
        localStorage.nickname = this.nickname
        window.location = '/'
      }).catch(reason => {
        this.showError(`Login failed: ${reason}`)
      }).then(() => {
        this.loading = false
      })
    }
  }
}
</script>

<style lang="less">
body {
  background-color: #6e0f6d;
}

#app {
  font-family: 'Microsoft YaHei UI', 'Microsoft YaHei', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;

  #particles-js {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  .card {
    width: 400px;
    margin: 80px auto 0 auto;
    text-align: center;

    header {
      font-size: 48px;
      color: #6e0f6d;
      font-weight: lighter;
    }

    footer {
      margin-top: 20px;
      font-size: 12px;
    }

    .el-tabs {
      margin-top: 10px;
    }

    .el-tabs__nav {
      float: none;
      display: inline-block;
      margin-left: 20px;
    }

    .el-input {
      margin-bottom: 10px;
    }

    .el-button {
      width: 100%;
    }
  }
}
</style>
