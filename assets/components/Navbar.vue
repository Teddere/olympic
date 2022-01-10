<template>
  <nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
      <i class="hamburger align-self-center"></i>
    </a>
    <div class="navbar-collapse collapse">
      <ul class="navbar-nav navbar-align">
        <li class="nav-item dropdown">
          <a
            class="nav-icon dropdown-toggle"
            href="#"
            id="alertsDropdown"
            data-bs-toggle="dropdown"
          >
            <div class="position-relative">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-bell align-middle"
              >
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              <!-- <i class="align-middle" data-feather="bell"></i> -->
              <span class="indicator" v-if="notification.length > 0">{{
                notification.length
              }}</span>
              <span class="indicator" v-else>0</span>
            </div>
          </a>

          <div
            class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0"
            aria-labelledby="alertsDropdown"
          >
            <div class="dropdown-menu-header" v-if="notification.length == 1">
              1 Nouvelle notification
            </div>
            <div
              class="dropdown-menu-header"
              v-else-if="notification.length > 1"
            >
              {{ notification.length }} Nouvelle notification
            </div>
            <div class="dropdown-menu-header" v-else>Accune notification</div>
            <div class="list-group">
              <a
                href="#"
                class="list-group-item"
                v-for="notif in notification"
                :key="notif.id"
              >
                <div class="row g-0 align-items-center">
                  <div class="col-2">
                    <i class="text-danger" data-feather="alert-circle"></i>
                  </div>
                  <div class="col-10">
                    <div class="text-dark">{{ notif.title }}</div>
                    <div class="text-muted small mt-1">
                      {{ notif.msg }}
                    </div>
                    <!-- <div class="text-muted small mt-1">30m ago</div> -->
                  </div>
                </div>
              </a>
            </div>
            <div class="dropdown-menu-footer">
              <a href="#" class="text-muted">Show all notifications</a>
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-icon dropdown-toggle"
            href="#"
            id="messagesDropdown"
            data-bs-toggle="dropdown"
          >
            <div class="position-relative">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-message-square align-middle"
              >
                <path
                  d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                ></path>
              </svg>
              <i class="align-middle" data-feather="message-square"></i>
            </div>
          </a>
          <div
            class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0"
            aria-labelledby="messagesDropdown"
          >
            <div class="dropdown-menu-header">
              <div class="position-relative" v-if="message.length == 1">
                1 Nouveau message
              </div>

              <div class="position-relative" v-else-if="message.length > 1">
                {{ message.length }} Nouveaux messages
              </div>
              <div class="position-relative" v-else>Accun message récu</div>
            </div>
            <div class="list-group">
              <a
                href="#"
                class="list-group-item"
                v-for="msg in message"
                :key="msg.id"
              >
                <div class="row g-0 align-items-center">
                  <div class="col-2">
                    <img
                      src="img/face/face-32.jpg"
                      class="avatar img-fluid rounded-circle"
                      alt="Vanessa Tucker"
                    />
                  </div>
                  <div class="col-10 ps-2">
                    <div class="text-dark">{{ msg.title }}</div>
                    <div class="text-muted small mt-1">
                      {{ msg.msg }}
                    </div>
                    <!-- <div class="text-muted small mt-1">15m ago</div> -->
                  </div>
                </div>
              </a>
            </div>
            <div class="dropdown-menu-footer">
              <a href="#" class="text-muted">Afficher tous les messages</a>
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-icon dropdown-toggle d-inline-block d-sm-none"
            href="#"
            data-bs-toggle="dropdown"
          >
            <i class="align-middle" data-feather="settings"></i>
          </a>

          <a
            class="nav-link dropdown-toggle d-none d-sm-inline-block"
            href="#"
            data-bs-toggle="dropdown"
          >
            <img
              src="img/face/face-1.jpg"
              class="avatar img-fluid rounded me-1"
            />
            <span class="text-dark">{{ f_name }} {{ l_name }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="pages-profile.html"
              ><i class="align-middle me-1" data-feather="user"></i> Profile</a
            >
            <a class="dropdown-item" href="#"
              ><i class="align-middle me-1" data-feather="pie-chart"></i>
              Analytics</a
            >
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.html"
              ><i class="align-middle me-1" data-feather="settings"></i>
              Settings & Privacy</a
            >
            <a class="dropdown-item" href="#"
              ><i class="align-middle me-1" data-feather="help-circle"></i> Help
              Center</a
            >
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/login">Se déconnecter</a>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</template>
<script>

export default {
  data() {
    return {
      notification: [],
      message: [],
    };
  },
  //   props: {
  //   u_id:string,
  //   f_name:string,
  //   l_name:string,
  //   avatar:string

  // },
  props: ["u_id", "f_name", "l_name", "avatar"],
  beforeMount() {
    this.loadMessage();
    this.loadNotification();
  },
  methods: {
    async loadMessage() {
      const response = await fetch("/loadAccount/message/" + this.u_id);
      const data = await response.json();
      this.message = data;
    },

    async loadNotification() {
      const response = await fetch("/loadAccount/notification/" + this.u_id);
      const data = await response.json();
      this.notification = data;
    },
  },
};
</script>
