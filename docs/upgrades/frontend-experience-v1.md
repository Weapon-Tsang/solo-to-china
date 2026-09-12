# Frontend experience v1 实施台账

任务依据：用户在本轮明确采用的 `Solo_to_China_Codex_Upgrade_Prompt_v1.0.txt`。本轮按阶段 0→1→2→3→4 推进；以下为最终结账。日期：2026-09-12。

基线：`main`，HEAD `f44ce1092ced93dfb47d9b3eae83d0d5e4b97086`；开始时已有 22 个已跟踪文件未提交。保留正确的 Planner、Survival Kit、FAQ、footer 与内容契约实现，没有 reset、拉取覆盖、回退旧截图或提交。初始哈希见 [baseline.json](baseline.json)，本机原始补丁在 `output/playwright/upgrade-baseline/existing.patch`；旧交接已归档。基线五套静态检查通过；基线缺少原生 PHP CLI，本轮增加 PHP-WASM 等价解析与实际 WordPress 检查。

最终版本：Parent **0.33.0**、Child **0.12.0**、Tools **0.26.0**、Registry **1.4.0**；Content Contract **2.1.0**、Publish Package **1.0.0** 保持兼容。开发验收时尚未 commit/push/deploy；用户随后已授权提交并推送 origin/main。生产部署仍未授权或执行。

**完成**表示实现及对应本地验收完成。**本地完成／外部待办**表示真实服务、CMS、生产设备或运营资料仍待处理，不代表生产验收完成。所有证据位于 [QA 报告及 evidence](../qa/frontend-upgrade-v1.md)。

路径缩写：P=`wp-content/themes/solo-to-china`；C=`wp-content/themes/solo-to-china-child`；T=`wp-content/plugins/solo-to-china-tools`。表内简写文件名在对应目录的 inc/includes、assets/js/css 中。

| ID | 阶段 | 状态 | 实际实施 | 主要位置 | 验证证据 | 外部待办 |
|---|---|---|---|---|---|---|
| BASE-01 | 0 | 完成 | 职责边界保留 | P/inc；C/assets；T/includes | 三套 WordPress 组合 | 无 |
| BASE-02 | 0 | 完成 | 保留已确认产品及正确 dirty 实现 | P/front-page.php、page.php、footer.php | 初始 patch/hash；原有检查 | 无 |
| BASE-03 | 0 | 完成 | 核实分支、HEAD、版本、环境 | docs/upgrades/baseline.json | 22 个原有已跟踪 dirty 文件 | 无 |
| PRE-01 | 0 | 完成 | 完整读取任务书并定位当前范围 | 本台账；docs/handoff/archive | 没有旧快照回退或生产写入 | 无 |
| PRE-02 | 0 | 完成 | 逐项记录实现、验证和外部依赖 | 本台账；docs/qa/frontend-upgrade-v1.md | 42 个 ID 均结账 | 无 |
| LINK-01 | 1 | 完成 | 唯一真实发布实体链接；缺失隐藏 | P/inc/entity-links.php、site-collections.php；T/places.php | 链接 HTTP 200；draft/password/type/重复实体 runtime | 无 |
| LINK-02 | 1 | 完成 | 真实 FAQ/联系/法律内容及真正 404 | P/functions.php、page.php、404.php | 16 路由；未知 404；管理员正文不覆写 | 无 |
| VIS-01 | 2 | 完成 | 暖白、蓝色交互、深墨正文；保留 Logo | C/design-system.css、site.css、home.css | 手机/桌面截图及视觉复查 | 无 |
| VIS-02 | 2 | 完成 | 阅读层级、行宽、间距、按钮和状态 | C/assets/css；P/experience-components.css | 200% 字体及 320–1440px | 无 |
| VIS-03 | 2 | 完成 | 图片区、轻工具卡、编辑列表各有用途 | P/front-page.php；C/home.css | 仅真实发布内容进入首页列表 | 无 |
| PAGE-01 | 2 | 完成 | 首页准备入口、真实指南、工具和收敛 Planner | P/front-page.php；C/home.css | 保留五入口/FAQ；两端截图 | 无 |
| PAGE-02 | 2 | 完成 | SSR 目录、无图紧凑标题、可选组件 | P/single.php、functions.php、experience-components.php | 重复/无 H2；步骤图及注释放大 | 无 |
| PAGE-03 | 2 | 完成 | Planner 跳转、Tools 层级及辅助票务 | P/page.php；T/shortcodes.php；C/tools.css | 核准短链、目录/独立路由 runtime | 无 |
| UX-01 | 2 | 完成 | More 初始状态、断点状态、焦点和减少动效 | P/main.js、main.css | 10 宽度；慢图、返回导航、无 JS | 无 |
| UX-02 | 2 | 完成 | Share 原生取消、复制回退、面板和多实例 | P/functions.php、main.js；C/article.css | Chromium/WebKit；无 Instagram 假分享 | 无 |
| UX-03 | 2 | 完成 | 唯一导航事件、SSR 唯一锚点、无 H2 无空目录 | P/functions.php、single.php、main.js；C/site.js | 初始 HTML、重复/无 H2、无 JS | 无 |
| CMP-01 | 3 | 完成 | 单一 authoring Registry，不按分类硬注入 | P/content-contract；P/inc | Registry 1.4.0：29 能力/26 页面块；旧契约 | 无 |
| CMP-02 | 3 | 完成 | 扩展 facts/steps/route/image/hero；新增地点/图注/相关文章 | P/inc/experience-components.php、cms-articles.php | Schema、媒体、重复/越界截图校验 | 无 |
| CMP-03 | 3 | 完成 | 已有优缺点/选项/住宿组件复用 | P/inc/content-renderers.php；playground-upgrade.php | 真实文章/Gallery；commercial harness | 无 |
| CMP-04 | 3 | 本地完成／外部待办 | Registry/Schema/Gallery/示例和同步说明 | contracts；docs/CMS_UPGRADE_1_4.md | JSON Schema；真实编辑器 | 独立 CMS 仓库联调 |
| FIND-01 | 3 | 完成 | 1–4 图追加/删除/排序、局部粘贴、Worker 和降级 | T/place-finder.js、image-worker.js | EXIF/透明/长图/低分辨率/损坏/大图双浏览器 | 无 |
| FIND-02 | 3 | 完成 | 状态机、取消、代次保护、超时与手动重试 | T/place-finder.js | A/B、迟到响应/finally、重复提交/超时 | 无 |
| FIND-03 | 3 | 本地完成／外部待办 | 服务器重编码、状态码、限流/预算/并发 | T/rest-api.php、providers.php、cost-controls.php | 实际 multipart + 本地 provider stub | 真实网关凭据、预算和上线配置 |
| FIND-04 | 3 | 完成 | 可信候选、实体与机位分开、目录值不可被模型覆盖 | T/providers.php、places.php、place-finder.js | 高/中/低、同名候选、已验证视点保护 | 无 |
| FIND-05 | 3 | 本地完成／外部待办 | 识别副本、去元数据、临时清理和隐私说明 | T/image-worker.js、rest-api.php、shortcodes.php | 客户端/服务器处理证据 | 厂商保留政策及真实日志配置 |
| TAXI-01 | 3 | 完成 | 版本化 JSON 目录、严格导入与回滚 | T/catalog.php、data；contracts/destination-catalog.schema.json | 9 条有来源记录；非法版本/字段/重复/日期不破坏活动数据 | 无 |
| TAXI-02 | 3 | 完成 | 本地优先、城市消歧、服务异常区别未知 | T/rest-api.php、places.php、tools.js | alias、503/504、未知/缺地址、识别直达 | 无 |
| TAXI-03 | 3 | 完成 | 原生 Driver dialog、长中文、安全区、复制/焦点回退 | T/shortcodes.php、tools.js、tools.css | 两浏览器；320/390/844 横屏及多实例 | 无 |
| AUX-01 | 3 | 本地完成／外部待办 | 旧票务 ID 兼容、中国日期及证据门槛 | T/shortcodes.php、tools.js | 过去/跨时区/过期/unknown fixture | 真实票务规则复核和实体深链录入 |
| AFF-01 | 3 | 本地完成／外部待办 | 保留核准 Planner URL、商业标识和按需事件代码 | P/commercial-components.php、experience-assets.php；C CSS | commercial harness 与 runtime | 生产账号真实归因复核 |
| PERF-01 | 1 | 完成 | 45 WebP、尺寸占位、hero eager/high、card lazy | P/assets/images、experience-assets.php | 390px hero 32,260 B；源图 2,009,572 B | 无 |
| PERF-02 | 1 | 完成 | 工具执行拆分及嵌套/同步块资源发现 | P/experience-assets.php；T 主文件/JS；C/functions.php | 首页不加载工具执行；三组合及 nested wp_block | 无 |
| PERF-03 | 1 | 本地完成／外部待办 | 实体索引和失效；工具响应 private/no-store | P/entity-links.php；T/rest-api.php；部署说明 | 错误响应也 no-store | 真实 CDN/代理/主机配置 |
| PERF-04 | 1 | 完成 | 实际资源和实验指标、测量边界记录 | docs/qa/frontend-upgrade-v1.md、evidence | 本地三次 LCP/CLS；不捏造旧 CWV/线上 INP | 无 |
| QUALITY-01 | 4 | 完成 | 无 JS 阅读/导航/目录、focus/live/dialog 和语义控件 | P/C/T UI；verify-tool-interactions.js | 无 JS、键盘、复制拒绝、减少动效、200% 字体 | 无 |
| QUALITY-02 | 4 | 完成 | 语义结构、真实链接、现有 SEO 所有权 | P/cms-articles.php、single.php、functions.php | 原有 runtime SEO/JSON-LD；无排名承诺 | 无 |
| TEST-01 | 4 | 完成 | 原有检查保留适配、PHP/WordPress/浏览器入口 | scripts/verify-upgrade.ps1 等 | PS 7.6.5、PHP-WASM 8.3、WP 7.0.4；原生 PHP CLI 未运行 | 无 |
| TEST-02 | 4 | 本地完成／外部待办 | 页面/尺寸/状态/Chrome/WebKit矩阵和截图 | scripts/verify-experience-browser.js、edges、final-states | 模拟视口及实际滚动局部截图 | 物理 iOS/Android/Safari 与生产网络 |
| TEST-03 | 4 | 完成 | DOM、接口异常、目录/预算原子性和旧 payload 回归 | scripts/verify-tool-interactions.js、upgrade-invariants.php 等 | JSON 证据；mock 不等于付费服务实测 | 无 |
| TEST-04 | 4 | 完成 | 资源、CLS、Worker 样本及实际图像复查 | verify-experience-browser.js、final-states.js | 3 次本地；Worker 样本 236ms，无所见 longtask | 无 |
| DEL-01 | 4 | 完成 | 代码、契约、台账、设计/工具/CMS/QA/部署/交接和安装包 | README；docs；dist | ZIP/manifest；fixture 和凭据不入包 | 无 |
| DEL-02 | 4 | 完成 | 最终版本、dirty、证据、测试和线上待办汇报 | docs/handoff；最终回复 | 不宣称已部署或外部联调已完成 | 无 |

全部 42 个 ID 已逐项结账，本地开发与验收工作完成。独立外部待办包括真实 provider 和隐私/花费配置、外部 CMS 联调、目的地/票务资料扩充、生产缓存/主机部署、物理设备与现场性能验收。详见 [部署说明](../deployment/frontend-upgrade-v1.md)；不要用本地 mock 结果替代这些步骤。
