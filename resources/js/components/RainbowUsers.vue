<template>
  <div class="row">
    <div class="col-lg-3 no-padding border-right-light">
      <ul class="list-group" v-for="user in users.data">
        <li class="list-group-item">
          <button @click="display(user.id)">
            {{ user.name }}
          </button>
        </li>
      </ul>
    </div>
    <div class="col-lg-9 no-padding" :id="message-box">
      <div class="container-fluid">
        <nav class="row nav pd-20">
          <a class="nav-link active" href="#">Active</a>
          <a class="nav-link" href="#">Link</a>
          <a class="nav-link" href="#">Link</a>
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </nav>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    mounted() {
      console.log('Component mounted.');
    },

    data : function() {
      return {
        users : [],
        user  : null
      }
    },

    ready : function () {
      this.created();
    },

    created : function() {
      axios
        .get('http://iris-messaging.test/all')
        .then(response => (this.users = response.data))
    },

    methods : {
      display: function(user_id) {
        axios
          .get('http://iris-messaging.test/user/' + user_id)
          .then(response =>
              (this.user = response.data),
          )
      }
    }
  }
</script>
