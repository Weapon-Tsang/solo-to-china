# SoloToChina 当前进度

更新：2026-09-12。最新事实以本文件、[42 项台账](../upgrades/frontend-experience-v1.md) 和 [QA 报告](../qa/frontend-upgrade-v1.md) 为准。旧交接已经保留在 archive/，不是当前实现要求。

本轮已实施任务书的阶段 0–4。开发分支为 `main`，基础 HEAD 为 `f44ce1092ced93dfb47d9b3eae83d0d5e4b97086`。保留开始时 22 个已跟踪 dirty 文件中的正确工作；没有 reset 或生产部署。验收后用户已授权将本轮完整成果提交并推送到 `origin/main`，实际提交与工作区状态以 Git 为准。不要用旧截图/版本回退正确实现。

| 部件 | 当前版本 |
|---|---|
| Parent Theme | 0.33.0 |
| Child Theme | 0.12.0 |
| Tools Plugin | 0.26.0 |
| Component Registry | 1.4.0 |
| Content Contract | 2.1.0 |
| Publish Package | 1.0.0 |

已完成：真实发布实体链接及索引/失效、45 个响应式图片、按内容加载资源；暖白/蓝色视觉与首页/文章/工具；More、唯一导航事件、SSR 目录、Share 与复制回退；Registry 兼容扩展、注释图片/地点信息/相关文章；1–4 图识别副本处理、Worker、取消和迟到请求保护、后端验证与预算；版本化目的地目录和原子导入/回滚；部分可信字段、司机模式和长名称固定操作栏；票务证据与中国日期规则。

既有 Planner 核准短链 `https://www.trip.com/t/bCPFQ85ZHW2`、Survival Kit/FAQ/footer 和已有商业组件保留。没有新增虚构景点详情、永久上传历史、用户账户或假票务链接。没有改变 Logo 原文件。

已验证：原有静态及 content/tools runtime、PHP 8.3 TOKEN_PARSE、commercial harness；三个 WordPress 7.0.4 安装组合；Chromium/WebKit 工具交互；16 页面/10 宽度/无 JS/减少动效/200% 字体；真实本地 multipart 异常和 Gutenberg 编辑器。详见 QA，原生 PHP CLI、物理设备、生产网关和外部 CMS 未测。不能把 mock 识别当作真实厂商准确率。

本地首页三次 LCP 768/796/772ms、CLS 0；390px 首图 32,260 字节。没有旧版可比 CWV 或线上 INP。超长整页截图有离屏漏绘，正文和页首的实际视口截图已补充；不得把截图工具限制误记为生产视觉通过。

交付：
- [设计规范](../design/frontend-experience-v1.md)
- [工具架构/隐私/成本](../architecture/tools-upgrade-v1.md)
- [CMS 同步说明](../CMS_UPGRADE_1_4.md)
- [QA 与可复现命令](../qa/frontend-upgrade-v1.md)
- [部署和回滚](../deployment/frontend-upgrade-v1.md)
- `dist/solo-to-china-theme.zip`、`dist/solo-to-china-child-theme.zip`、`dist/solo-to-china-tools-plugin.zip`、`dist/release-manifest.txt`。dist 被 Git 忽略，包须与 manifest 一起交付。

尚未完成的外部事项：选择/配置实际 HTTPS 识别网关和服务端凭据，核实厂商保留政策/日志/预算；在独立 CMS 仓库刷新契约并做 draft 发布回读；复核并补录更多地点到达字段、真实票务规则/深链；在用户授权后部署 staging/production、设置 CDN/代理及请求限制，执行物理设备和线上性能验收。其余本地开发项已完成，不应重新拆成仅计划的下一轮任务。
