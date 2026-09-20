# v3.0 本地前端验收记录（2026-09-19–20，DEVELOPMENT）

本文件仅记录当前未提交工作树与本地 WordPress Playground。权威六领域总台账在 CMS 仓库 `docs/audit/UNIFIED_RECTIFICATION_V3.md`；2026-09-12 的 `frontend-upgrade-v1.md` 是历史记录，不作本轮证据。无生产部署、线上活动主题检查或生产数据刷新。

## 当前环境与代码身份

- WordPress `http://127.0.0.1:9400/`，父/子主题与 Tools 插件从本机仓库挂载；父主题 main.js、子主题 site/article/content/home/tools CSS 实际加载。子主题 `0.13.0-dev`，非线上版本。
- 前端 Git 基线 `d1e0c078add5b988833c11a79ca995d59a237c72`，修改未提交。Contract 1.4.1，Registry checksum `d83d9c0df8757c96cdc3dba8f12ce4dd3d2163d2ab884af07ad3abc68276c880`。
- fixture 使用 `scripts/playground-fixtures.php`、`scripts/playground-upgrade.php`，长文章、两种旧/新商业卡、目录/Share、首页、Gallery、Tools 都是隔离测试内容；无付费模型调用。

## 本轮实际浏览器观察

| 范围 | 实测结果 | 证据 / 限制 |
| --- | --- | --- |
| 文章桌面/手机 | 暖白阅读背景、深色紧凑页首、正文与商业卡层级；390px 没有页面横向溢出。目录原生 details，Step by step 定位后标题在 156px 左右，返回清除 hash。 | `output/playwright/v3-after-long-guide-1440-viewport-v2.png`、`v3-after-long-guide-390-viewport.png`、`v3-after-toc-mobile-open.png`；首轮手机图在末次小字号微调前，须补同条件新图。 |
| Share | 390px 面板已从原先底部截断调整至可见区域；Escape 关闭、焦点回按钮。跨日重新启动浏览器后以 `prefers-reduced-motion: reduce` 再开弹层，内容可操作，计算 transition `1e-05s`。 | `output/playwright/v3-after-share-mobile-fixed.png`、`output/playwright/v3-reduced-motion-share-390.png`；复制失败/原生分享/外点/安全区仍待全测。 |
| 首页 More | 390px 连续 10 次点击，展开状态 true/false、可见城市卡 8/4、按钮焦点均同步；桌面自动显示所有卡，隐藏 More。跨日再次点击展开并看到 `Show fewer`，导航菜单展开后 Escape 关闭、焦点回按钮。 | 本轮 Playwright CLI 实操；低速图片和 noJS 待复核。 |
| Taxi Card | 390px 输入未知长中文地址得到未核实提示；输入已有 Forbidden City 目录项生成保留“入口/落客未确认”的中文司机卡，Driver Mode Escape 后焦点返回，页面无横向溢出。 | 本地目录解析不是模型识别。未知地址 `/wp-json/stc/v1/taxi-card` 返回预期业务 404，控制台资源错误须与未捕获异常区分。 |
| Place Finder | 未配置视觉供应商时明确禁用识别并说明原因，没有伪装 mock 结果。 | 实际识别、取消/成功/部分准确待真实模型授权，NOT TESTED。 |
| 商业组件 | Gallery 现有五个 ID 均被真实 renderer 渲染；booking card 桌面/390px 截图已目视复核，层级和触控 CTA 可读无溢出。CMS 经本地 HTTP 投递的旧 `affiliate_cta` 和新 `affiliate_booking_card` 非空旧默认长句均显示 Paid link，链接与 rel 保留。 | `output/playwright/v3-commercial-booking-1440.png`、`v3-commercial-booking-390.png`；其余商业 variant、200%/无图/键盘/事件仍缺。 |
| 契约/运行 | `pwsh -NoProfile -File scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9400` PASS：架构、Registry、内容契约、Web Tools、项目、Experience 资源、内容 Runtime、Tools Runtime。 | PHP CLI 缺失，独立 PHP CLI lint skipped；Playground 自带 PHP 蓝图实际运行。 |

## 未完成的 V / MOTION 门槛

V01–V04、V07、V09、V10、V11、V12 仅部分实测；V05/V06/V08 尚无完整浏览器矩阵。MOTION-04 More、MOTION-02 Share、移动导航的开闭/焦点及 Share 的 reduced-motion 有实操；其余 M01/M03/M05/M06 及 noJS、WebKit、200% 文本、320/375/768/1440 全路径、低速/失败图和重复性能测量均未通过本轮完整验收。图片截图是静态证据，不能替代动效时序或无障碍结论。

本地可操作地址：`http://127.0.0.1:9400/`、`/upgrade-long-guide/`、`/design-system/`、`/tools/find-this-place/`、`/tools/taxi-card/`。服务需由 `scripts/start-preview.ps1` 保持运行；Playground 重启会重建隔离数据，CMS 草稿 ID 可能变化，需重跑 CMS 仓库本地投递脚本。
