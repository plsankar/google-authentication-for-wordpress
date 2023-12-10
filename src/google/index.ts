import "./index.scss";

function create_google_login_button(redirectTo = "") {
    const button = document.createElement("a");
    button.classList.add("gauthwp-google-button");
    button.id = "gauthwp-google-button";
    button.href = window.gauthwp_login_google.args.authUrl;

    if (redirectTo !== "") {
        const authUrl = new URL(window.gauthwp_login_google.args.authUrl);
        authUrl.searchParams.append("redirect_url", redirectTo);
        button.href = authUrl.href;
    }

    const span = document.createElement("span");
    span.innerHTML = "SignIn With Google";

    button.append(span);

    const img = document.createElement("img");
    img.alt = span.innerHTML;
    img.src = `${window.gauthwp_login_google.args.pluginurl}/assets/btn_google_signin_dark_normal_web@2x.png`;

    button.append(img);

    return button;
}

function add_google_login_to_form(formEl: HTMLFormElement) {
    if (window.gauthwp_login_google.args.show_on_login == false) {
        return;
    }
    formEl?.appendChild(create_google_login_button());
}

if (document.body.classList.contains("login")) {
    if (document.querySelector("#loginform")) {
        add_google_login_to_form(
            document.querySelector<HTMLFormElement>("#loginform")!
        );
    }
    if (document.querySelector("#registerform")) {
        add_google_login_to_form(
            document.querySelector<HTMLFormElement>("#registerform")!
        );
    }
}

document.querySelectorAll("[data-gauthwp-google-button]").forEach((parentEl) => {
    parentEl.appendChild(create_google_login_button(window.location.href));
});
