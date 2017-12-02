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
      :moveSpeed="2"
      :hoverEffect="false"
      :clickEffect="false"/>
    <el-card class="card">
      <div class="title">ZeroNote</div>
      <el-tabs value="login">
        <el-tab-pane label="Login" name="login">
          <el-input v-model="email" placeholder="E-mail"></el-input>
          <el-input v-model="password" placeholder="Password"></el-input>
          <el-button type="primary">Login</el-button>
        </el-tab-pane>
        <el-tab-pane label="Register" name="register">
          <el-input v-model="email" placeholder="E-mail"></el-input>
          <el-input v-model="password" placeholder="Password"></el-input>
          <el-input v-model="password2" placeholder="Re-type password"></el-input>
          <el-input v-model="nickname" placeholder="Nickname (Optional)"></el-input>
          <el-button type="primary">Register</el-button>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script>
export default {
  data () {
    return {
      email: '',
      password: '',
      password2: '',
      nickname: ''
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

    .title {
      font-size: 48px;
      color: #6e0f6d;
      font-weight: lighter;
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
