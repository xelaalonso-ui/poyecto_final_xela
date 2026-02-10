document.querySelectorAll("a,button").forEach(el=>{
    el.addEventListener("mouseover",()=>{
        el.style.transform="scale(1.05)";
    });

    el.addEventListener("mouseout",()=>{
        el.style.transform="scale(1)";
    });
});
