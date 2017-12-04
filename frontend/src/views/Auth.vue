<template>
  <div id="app">
    <vue-particles
      color="#ffffff"
      :particleOpacity="0.3"
      :particlesNumber="60"
      shapeType="circle"
      :particleSize="10"
      linesColor="#ffffff"
      :linesWidth="1"
      :lineLinked="true"
      :lineOpacity="0.2"
      :linesDistance="150"
      :moveSpeed="1"
      :hoverEffect="false"
      :clickEffect="false"/>
    <el-card class="card">
      <header>ZeroNote</header>
      <el-tabs value="login">
        <el-tab-pane label="Existing user" name="login">
          <el-input v-model="email" type="email" placeholder="E-mail" :disabled="loading" required/>
          <el-input v-model="password" type="password" placeholder="Password" :disabled="loading" required/>
          <el-button type="primary" :loading="loading" v-on:click="loginClick">Login</el-button>
        </el-tab-pane>
        <el-tab-pane label="New user" name="register">
          <el-input v-model="email" type="email" placeholder="E-mail" :disabled="loading" required/>
          <el-input v-model="password" type="password" placeholder="Password" :disabled="loading" required/>
          <el-input v-model="password2" type="password" placeholder="Re-type password" :disabled="loading" required/>
          <el-input v-model="nickname" placeholder="Nickname (Optional)" :disabled="loading"/>
          <el-button type="primary" :loading="loading" v-on:click="regClick">Register</el-button>
        </el-tab-pane>
      </el-tabs>
      <footer>&copy; 2017 DeepAQ.</footer>
    </el-card>
  </div>
</template>

<script>
import api from '../utils/api'

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
    postLogin (data) {
      this.$message({
        showClose: true,
        type: 'success',
        message: 'Login succeeded, redirecting...'
      })
      localStorage.token = data.token
      localStorage.email = this.email
      localStorage.nickname = data.nickname
      window.location = '/'
    },
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
      }).then(this.postLogin).catch(reason => {
        this.showError(`Login failed: ${reason}`)
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
        data.nickname = this.nickname
        this.postLogin(data)
      }).catch(reason => {
        this.showError(`Login failed: ${reason}`)
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
  #particles-js {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
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

    .el-input {
      margin-bottom: 10px;
    }

    .el-button {
      width: 100%;
    }
  }
}
</style>
