const thongKe = [
  { nhan: "Bảng dữ liệu", giaTri: "11", ghiChu: "Đúng phạm vi Giai đoạn 2" },
  { nhan: "Món ăn mẫu", giaTri: "8", ghiChu: "Tên món Việt Nam thực tế" },
  { nhan: "Khách hàng mẫu", giaTri: "3", ghiChu: "Có email và số điện thoại" },
  { nhan: "File import", giaTri: "SQL", ghiChu: "Tương thích phpMyAdmin" },
];

const bangDuLieu = [
  {
    ten: "roles",
    chucNang: "Lưu vai trò: Quản trị viên, Thu ngân, Nhân viên phục vụ, Nhân viên bếp, Khách hàng.",
    quanHe: "Một vai trò có nhiều người dùng.",
  },
  {
    ten: "users",
    chucNang: "Lưu tài khoản đăng nhập, email, số điện thoại, mật khẩu và trạng thái.",
    quanHe: "Thuộc một vai trò, liên kết đến khách hàng hoặc nhân viên.",
  },
  {
    ten: "customers",
    chucNang: "Lưu thông tin khách hàng đặt bàn trực tuyến hoặc trực tiếp.",
    quanHe: "Có nhiều phiếu đặt bàn và nhiều lịch sử chatbot.",
  },
  {
    ten: "employees",
    chucNang: "Lưu hồ sơ nhân viên, mã nhân viên, chức vụ, ca làm và lương.",
    quanHe: "Thuộc một người dùng, xử lý đặt bàn và thanh toán.",
  },
  {
    ten: "tables",
    chucNang: "Lưu bàn ăn, khu vực, số ghế và trạng thái bàn.",
    quanHe: "Một bàn có nhiều lượt đặt bàn theo thời gian.",
  },
  {
    ten: "categories",
    chucNang: "Lưu danh mục món ăn như Món chính, Món khai vị, Đồ uống.",
    quanHe: "Một danh mục có nhiều món ăn.",
  },
  {
    ten: "foods",
    chucNang: "Lưu món ăn, giá bán, mức cay, thời gian chế biến và trạng thái bán.",
    quanHe: "Thuộc một danh mục, xuất hiện trong chi tiết đặt món.",
  },
  {
    ten: "reservations",
    chucNang: "Lưu phiếu đặt bàn, thời gian, số khách, nguồn đặt và trạng thái.",
    quanHe: "Thuộc khách hàng, có thể gắn bàn ăn và nhân viên phụ trách.",
  },
  {
    ten: "reservation_details",
    chucNang: "Lưu từng món ăn khách đặt trước, số lượng, đơn giá và ghi chú bếp.",
    quanHe: "Thuộc một phiếu đặt bàn và một món ăn.",
  },
  {
    ten: "payments",
    chucNang: "Lưu thanh toán, giảm giá, phí phục vụ, VAT, tổng tiền và phương thức thanh toán.",
    quanHe: "Thuộc một phiếu đặt bàn, có thể gắn nhân viên thu ngân.",
  },
  {
    ten: "chatbot_histories",
    chucNang: "Lưu lịch sử hội thoại chatbot, ý định người dùng và độ tin cậy.",
    quanHe: "Có thể gắn khách hàng và phiếu đặt bàn.",
  },
];

const duLieuMau = [
  {
    nhom: "Danh mục",
    giaTri: ["Món chính", "Món khai vị", "Món tráng miệng", "Đồ uống", "Món cay"],
  },
  {
    nhom: "Món ăn",
    giaTri: ["Gà xào cay", "Cá chép sốt cải xanh", "Lẩu hải sản siêu cay", "Baba om chuối đậu", "Bánh xèo tôm", "Canh chua cá lóc"],
  },
  {
    nhom: "Khách hàng",
    giaTri: ["Nguyễn Văn An", "Trần Thị Mai", "Lê Hoàng Nam"],
  },
  {
    nhom: "Bàn ăn",
    giaTri: ["Bàn 01", "Bàn 02", "Bàn 03", "Bàn VIP 01", "Bàn sân vườn 01"],
  },
];

document.addEventListener("DOMContentLoaded", () => {
  document.querySelector("#summaryGrid").innerHTML = thongKe
    .map((item) => `
      <article class="kpi-card">
        <span>${item.nhan}</span>
        <strong>${item.giaTri}</strong>
        <small>${item.ghiChu}</small>
      </article>
    `)
    .join("");

  document.querySelector("#databaseTable").innerHTML = bangDuLieu
    .map((bang) => `
      <tr>
        <td><strong>${bang.ten}</strong></td>
        <td>${bang.chucNang}</td>
        <td>${bang.quanHe}</td>
      </tr>
    `)
    .join("");

  document.querySelector("#sampleList").innerHTML = duLieuMau
    .map((nhom) => `
      <article class="sample-card">
        <h3>${nhom.nhom}</h3>
        <p>${nhom.giaTri.join(", ")}</p>
      </article>
    `)
    .join("");
});
