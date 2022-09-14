// function nl2br(str) {
//     var res = str.replace(/\r\n/g, "<br>");
//     res = res.replace(/(\n|\r)/g, "<br>");
//     return res;
// }

const nl2br = (str) => {    // ↑アロー関数で書き換え
    var res = str.replace(/\r\n/g, "<br>");
    res = res.replace(/(\n|\r)/g, "<br>");
    return res;
}

export { nl2br }