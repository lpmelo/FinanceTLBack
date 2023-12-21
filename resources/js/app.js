import "./bootstrap";

const navigateTo = (url) => {
    return (window.location.href = url);
};

window.navigateTo = navigateTo;
