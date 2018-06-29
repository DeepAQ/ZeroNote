<template>
  <div id="page">
    <div class="row">
      <div>
        <h3><i class="el-icon-time"></i> Latest notes</h3>
        <div class="item" v-for="note in latest">
          <div class="title">
            <i class="el-icon-document"></i>
            <router-link :to="{ name: 'read', params: { id: note.id }}">
              {{ note.title }}
            </router-link>
          </div>
          <div class="info">
            <i class="el-icon-star-on"></i> {{ note.upvotes }}&nbsp;&nbsp;
            <i class="el-icon-time"></i> {{ (new Date(note.updated * 1000)).toLocaleString() }}
          </div>
          <div class="preview">
            {{ note.preview }} ……
          </div>
        </div>
      </div>
      <div>
        <h3><i class="el-icon-star-off"></i> Popular notes</h3>
        <div class="item" v-for="note in popular">
          <div class="title">
            <i class="el-icon-document"></i>
            <router-link :to="{ name: 'read', params: { id: note.id }}">
              {{ note.title }}
            </router-link>
          </div>
          <div class="info">
            <i class="el-icon-star-on"></i> {{ note.upvotes }}&nbsp;&nbsp;
            <i class="el-icon-time"></i> {{ (new Date(note.updated * 1000)).toLocaleString() }}
          </div>
          <div class="preview">
            {{ note.preview }} ……
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      search: '',
      latest: [],
      popular: []
    }
  },
  mounted () {
    api('hub/trends', {}).then(data => {
      this.latest = data.latest
      this.popular = data.popular
    }).catch(reason => {
      this.$message({
        showClose: true,
        type: 'error',
        message: reason
      })
    })
  }
}
</script>

<style lang="less" scoped>
#page {
  width: 100%;
  
  .row {
    margin-top: 10px;
    display: flex;

    >div {
      flex: 1;
      margin: 0 10px;

      h3 {
        margin: 10px 0;
      }
    }
  }

  .item {
    padding-top: 10px;
    margin-bottom: 10px;
    border-top: solid 1px #ccc;

    .title {
      font-size: 20px;
      color: #6e0f6d;

      a {
        color: #6e0f6d;
        text-decoration: none;
      }
    }

    .info {
      font-size: 14px;
      color: #aaa;
    }

    .preview {
      font-size: 12px;
      color: #666;
    }
  }
}
</style>
