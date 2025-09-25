<template>
    <main>
        <Loader v-if="isLoading" />
        <div v-else>
            <p v-if="errors" class="error">{{ errors }}</p>
            <!-- <p v-if="success" class="success">Your email has been verified! Redirecting...</p> -->
        </div>
    </main>
</template>

<script>
import {
    ROUTE_USER_HOME,
    ROUTE_ADMIN_HOME,
    ROLE_ADMIN,
    ROLE_USER
} from "../../constants.js";
import store from "../../store.js";
import Loader from "../components/Loader.vue";

export default {
    name: "VerifyEmail",
    components: { Loader },
    data() {
        return {
            isLoading: true,
            errors: "",
            success: false,
        };
    },
    methods: {
        verifyEmail(token) {

            this.axios
                .post("verify-email", { token })
                .then((response) => {
                    const user = response.data.user;

                    // Store user + token
                    localStorage.setItem("user", JSON.stringify(user));
                    localStorage.setItem("role", user.role);
                    localStorage.setItem("sanctum_token", response.data.token);

                    store.dispatch("setAuthUser");

                    this.success = true;

                    // Redirect based on role
                    setTimeout(() => {
                        if (user.role === ROLE_ADMIN) {
                            this.$router.push(ROUTE_ADMIN_HOME);
                        } else {
                            this.$router.push(ROUTE_USER_HOME);
                        }
                    }, 1500);
                })
                .catch((error) => {
                    this.errors = this.$flattenErrors(error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    },
    created() {
        const token = this.$route.params.token;
        this.verifyEmail(token);
    },
};
</script>

<style>
.error {
    color: red;
    font-weight: bold;
}
.success {
    color: green;
    font-weight: bold;
}
</style>
