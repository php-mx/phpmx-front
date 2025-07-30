document.addEventListener("DOMContentLoaded", () => {
    window.onpopstate = () => location.reload();
    document.body.insertAdjacentHTML("beforeend", "<div id='ALERT'></div>");
    document.body.insertAdjacentHTML("beforeend", "<div id='ASIDE'></div>");
    document.body.querySelectorAll("script:not([static])").forEach((tag) => tag.setAttribute("static", ""));
    mx.core.run();
    for (const alert of currentAlert) mx.alert(alert[0], alert[1]);
    mx.update.scroll();
});

const app = {};

const mx = {};

mx.working = false;
mx.registred = false;

mx.core = {
    registred: {},
    instanceVue: {},
    run() {
        Object.keys(mx.core.registred).forEach((querySelector) =>
            document.body.querySelectorAll(querySelector).forEach((element) => {
                if (element.closest('[data-vue]')) return;
                mx.core.registred[querySelector](element);
                element.setAttribute("static", "");
            })
        );
        document.body.querySelectorAll("script:not([static])").forEach((tag) => {
            if (tag.closest('pre')) return;
            eval(tag.innerHTML);
            tag.setAttribute("static", "");
        });
    },
    register(querySelector, action) {
        mx.core.registred[querySelector] = action;
    },
    request(url = null, method = "get", data = {}, header = {}, useWorking = true) {
        return new Promise(function (resolve, reject) {
            if (useWorking && mx.working) return reject("working");

            if (useWorking) mx.working = true;
            document.body.classList.add("__working__");

            var xhr = new XMLHttpRequest();

            url = url ?? window.location.href;

            xhr.open(method, url, true);
            xhr.setRequestHeader("Request-Partial", true);

            for (let key in header) xhr.setRequestHeader(key, header[key]);

            xhr.responseType = "json";

            xhr.onload = () => {
                if (useWorking) mx.working = false;
                document.body.classList.remove("__working__");

                let resp = xhr.response;

                if (!resp.info || !resp.info.mx)
                    resp = {
                        info: {
                            mx: false,
                            error: xhr.status > 399,
                            status: xhr.status,
                        },
                        data: resp,
                    };

                if (Array.isArray(resp.info.alert)) {
                    for (const a of resp.info.alert) mx.alert(a[0], a[1]);
                } else if (resp.info.alert) {
                    mx.alert(resp.info.alert);
                }

                if (resp.info.location ?? false) {
                    mx.go(resp.info.location);
                    return reject("redirect");
                }

                return resolve(resp);
            };

            xhr.send(data);
        });
    },
    loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.head.querySelectorAll(`script[src="${src}"]`).length) return resolve();
            let script = document.createElement("script");
            script.async = "true";
            script.src = src;
            script.onload = () => resolve();
            document.head.appendChild(script);
        });
    },
};

mx.update = {
    content(content) {
        let element = document.getElementById("CONTENT");

        Object.entries(mx.core.instanceVue).forEach(([id, app]) => {
            const el = document.getElementById(id);
            if (el && element.contains(el)) {
                app.unmount();
                delete mx.core.instanceVue[id];
            }
        });

        element.innerHTML = content;
        mx.core.run();
    },
    layout(content, state) {
        let element = document.getElementById("LAYOUT");

        Object.entries(mx.core.instanceVue).forEach(([id, app]) => {
            const el = document.getElementById(id);
            if (el && element.contains(el)) {
                app.unmount();
                delete mx.core.instanceVue[id];
            }
        });

        element.innerHTML = content;
        document.body.dataset.state = state;
        mx.core.run();
    },
    location(url) {
        if (url != window.location) history.pushState({ urlPath: url }, null, url);
    },
    head(head) {
        const desc = document.head.querySelector('meta[name="description"]');
        const favicon = document.head.querySelector('link[rel="icon"]');

        if (head.title) document.title = head.title;
        if (desc && head.description) desc.setAttribute("content", head.description);
        if (favicon && head.favicon) favicon.setAttribute("href", head.favicon);

    },
    aside(content, position = null) {
        const element = document.getElementById("ASIDE");

        Object.entries(mx.core.instanceVue).forEach(([id, app]) => {
            const el = document.getElementById(id);
            if (el && element.contains(el)) {
                app.unmount();
                delete mx.core.instanceVue[id];
            }
        });

        if (content) {
            element.innerHTML = '<span class="aside-backcover" data-aside></span>' + content;
            element.dataset.position = position ?? element.dataset.position;
            document.body.classList.add('__aside__');
            mx.core.run();
        } else {
            element.innerHTML = '';
            element.dataset.position = '';
            document.body.classList.remove('__aside__');

        }
    },
    scroll(area = null) {
        if (!area) {
            const hash = location.hash;
            if (hash && hash.length > 1)
                area = decodeURIComponent(hash.slice(1));
        }

        if (area) {
            const anchor = document.querySelector(`[data-area="${area}"]`);

            if (anchor) {
                const top = anchor.getBoundingClientRect().top + window.scrollY - 100;
                window.scrollTo({ top, behavior: 'smooth' });
                return;
            }
        }

        window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
    }
};

mx.go = (url) => {
    url = new URL(url, document.baseURI).href
    if (new URL(url).hostname != new URL(window.location).hostname) return mx.redirect(url);
    let state = document.body.dataset.state;
    mx.core
        .request(url, "get", {}, { "State": state })
        .then((resp) => {
            if (!resp.info.mx) return mx.redirect(url);

            if (resp.info.error && resp.data == null) return;

            mx.aside.close();

            mx.update.head(resp.data.head);

            mx.update.location(url);

            if (resp.data.state == state) {
                mx.update.content(resp.data.content);
            } else {
                mx.update.layout(resp.data.content, resp.data.state);
            }

            mx.update.scroll();
            return;
        })
        .catch(() => null);
};

mx.aside = {
    open(url, position) {
        if (!["top", "left", "right", "bottom", "center", "full", null].includes(position)) return mx.aside.close();
        url = new URL(url, document.baseURI).href
        if (new URL(url).hostname != new URL(window.location).hostname) return mx.redirect(url);
        mx.core
            .request(url, "get", {}, { "Request-Aside": true })
            .then((resp) => {
                if (!resp.info.mx) return mx.redirect(url);
                if (resp.info.error && resp.data == null) return;
                mx.update.aside(resp.data.content, position);
                return;
            })
            .catch(() => null);
    },
    close() {
        mx.update.aside(null);
    }
};

mx.redirect = (url) => {
    window.location.href = url;
    return false;
};

mx.alert = (content, type) => {
    const alert = document.getElementById("ALERT");

    let normalizedType = 'neutral';
    let svg = `[#ICON:alert-neutral]`;

    if (type === true) {
        normalizedType = 'success';
        svg = `[#ICON:alert-success]`;
    }

    if (type === false) {
        normalizedType = 'error';
        svg = `[#ICON:alert-error]`;
    }

    const div = document.createElement("div");
    div.className = normalizedType;
    div.innerHTML = `${svg}<span>${content}</span>`;

    alert.appendChild(div);
    div.setAttribute("static", "");
    setTimeout(() => div.remove(), 5000);
};

mx.copy = (copyText, alertText = null) => {
    const textarea = document.createElement("textarea");
    textarea.value = copyText;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";

    document.body.appendChild(textarea);

    textarea.select();
    textarea.setSelectionRange(0, 99999);
    document.execCommand("copy");

    document.body.removeChild(textarea);

    if (alertText) mx.alert(alertText, true);
};

mx.debounce = (func, wait) => {
    let timer = null;
    return () => {
        clearTimeout(timer);
        timer = setTimeout(func, wait);
    };
};

mx.submit = (form, appentData = {}) => {
    const notify = form.dataset.notify ? document.getElementById(form.dataset.notify) : form.querySelector("[data-notify]");

    if (notify) notify.innerHTML = "";

    form.querySelectorAll("[data-error]").forEach((el) => {
        el.removeAttribute("data-error");
    });

    let url = form.action;
    let state = document.body.dataset.state;
    let header = { "State": state, 'Request-Submitting': true };
    let data = new FormData(form);

    appentData.formKey = form.getAttribute("data-form-key");
    for (const [key, value] of Object.entries(appentData)) data.append(key, value);

    mx.core
        .request(url, form.getAttribute("method") ?? "post", data, header)
        .then((resp) => {
            if (resp.info.error && form.dataset.error) return eval(form.dataset.error)(resp);

            if (!resp.info.error && form.dataset.success) return eval(form.dataset.success)(resp);

            if (resp.data) {
                mx.update.head(resp.data.head);
                mx.update.location(url);
                if (resp.data.state == state) mx.update.content(resp.data.content);
                else mx.update.layout(resp.data.content, resp.data.state);
                mx.update.scroll();
                return;
            }

            if (resp.info.error && resp.info.field) {
                const label = form.querySelector(`[data-input="${resp.info.field}"]`);
                if (label) label.setAttribute("data-error", resp.info.message ?? "true");
            }

            if (notify) {
                let spanClass = `sts_` + (resp.info.error ? "erro" : "success");
                let message = resp.info.message ?? (resp.info.error ? "erro" : "ok");
                notify.innerHTML = `<span class='${spanClass}'>${message}</span>`;
            }
        })
        .catch(() => null);
};

mx.vue = (component, elementId) => {
    mx.core
        .loadScript("/assets/third/vue.js")
        .then(() => {
            const el = document.getElementById(elementId);

            if (!el) throw new Error(`Element #${elementId} not found`);

            if (mx.core.instanceVue[elementId]) {
                mx.core.instanceVue[elementId].unmount();
                delete mx.core.instanceVue[elementId];
            }

            el.setAttribute("data-vue", "true");

            mx.core.instanceVue[elementId] = Vue.createApp(component());
            mx.core.instanceVue[elementId].mount(el);
        })
        .catch((e) => console.error("impossible to load [vue.js]", e));
};

mx.encapsulate = (value) => {
    return JSON.stringify(value);
};

mx.decapsulate = (value) => {
    return JSON.parse(value);
};

mx.api = (url, method, data) => {
    return mx.core.request(url, method, data, { "Request-Api": true }, false);
};

mx.uid = () => {
    return "_" + Date.now().toString(36) + Math.random().toString(36).substr(2);
};

mx.importFieldValue = (fieldId) => {
    return mx.decapsulate(document.getElementById(fieldId).value);
};

mx.exportFieldValue = (fieldId, value) => {
    document.getElementById(fieldId).value = mx.encapsulate(value);
};

mx.submitForm = (formId) => {
    document.getElementById(formId).requestSubmit();
};

mx.core.register("[href]:not([static]):not([href=''])", (element) => {
    element.addEventListener("click", (event) => {
        event.preventDefault();
        const url = element.href ?? element.getAttribute("href");
        if (element.hasAttribute("data-aside")) {
            mx.aside.open(url, element.dataset.aside || null);
        } else {
            mx.go(url);
        }
    });
});

mx.core.register('[data-aside]:not([static])', (element) => {
    element.addEventListener("click", (event) => {
        event.preventDefault();
        mx.aside.close();
    });
});

mx.core.register("[href]:not([href=''])", (element) => {
    const elementUrl = new URL(element.getAttribute("href"), document.baseURI);
    const currentUrl = new URL(window.location.href);

    const url = elementUrl.origin + elementUrl.pathname + elementUrl.search + "/";
    const href = currentUrl.origin + currentUrl.pathname + currentUrl.search + "/";

    element.classList.remove("active-link");
    element.classList.remove("current-link");

    if (href.startsWith(url)) element.classList.add("active-link");
    if (href === url) element.classList.add("current-link");
});

mx.core.register("form[data-form-key]:not([static])", (element) => {
    element.addEventListener("submit", async (ev) => {
        ev.preventDefault();
        mx.submit(element);
    });
});
