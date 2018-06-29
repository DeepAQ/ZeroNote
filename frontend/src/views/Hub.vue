<template>
  <el-container id="app">
    <el-header height="50px">
      <div class="title">ZeroNote</div>
      <div class="hub">Hub</div>
      <div class="nav">
        <a href="/">Home</a>
        <router-link to="/">Hub</router-link>
      </div>
      <UserInfo/>
    </el-header>
    <el-container>
      <div style="width: 100%;">
        <el-input placeholder="Search public notes ..." v-model="search" v-on:keyup.enter.native="searchClick">
          <el-button slot="append" icon="el-icon-search" @click="searchClick">
            Search
          </el-button>
        </el-input>
        <router-view/>
      </div>
    </el-container>
  </el-container>
</template>

<script>
import UserInfo from '../components/UserInfo'

export default {
  components: { UserInfo },
  data () {
    return {
      search: ''
    }
  },
  watch: {
    
  },
  mounted () {
    
  },
  methods: {
    searchClick () {
      if (this.search != '') {
        this.$router.push({ name: 'search', params: { kw: this.search }})
      }
    }
  }
}
</script>

<style lang="less">
#app {
  .el-header {
    background-color: #6e0f6d;
    color: whitesmoke;
    display: flex;
    align-items: center;

    .title {
      font-size: 28px;
      font-weight: lighter;
      cursor: pointer;
    }

    .hub {
      margin-left: 5px;
      background: whitesmoke;
      color: #6e0f6d;
      height: 30px;
      line-height: 30px;
      padding: 0 5px;
      border-radius: 5px;
    }

    .nav {
      flex: 1;
      padding-left: 20px;
      
      a {
        color: whitesmoke;
        text-decoration: none;
        margin-right: 10px;
      }
    }

    .el-dropdown {
      color: whitesmoke;
    }
  }

  .el-container {
    width: 1000px;
    margin: 20px auto;

    .el-input {
      margin-bottom: 10px;
    }
  }
}
</style>
