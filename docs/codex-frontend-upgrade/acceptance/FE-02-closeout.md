# FE-02 v1.1 本轮收尾验收（2026-09-27）

> 历史报告保留。当前判定与新增证据见 [FE-02 v1.2 本地续修验收](FE-02-closeout-v1.2.md)；下文 v1.1 的未完成项不自动代表当前状态。

本轮已补齐可执行的无 JS 浏览流程、真实查询失败、交互计时和分层性能证据，并修复四个公开字段漏搜。**没有全部通过：FS-20 核心正文仍有明确缺口；FS-32 当前环境未达 500ms。** 不建议进入发布。生产回填、真实 SEO/设备兼容分别列项，不能用一个 PENDING_ENV 掩盖这些差别。

## 本轮范围与代码

- 仓库 `C:\Users\Mloong\Documents\ChatGPT\solo-to-china`，origin `https://github.com/Weapon-Tsang/solo-to-china.git`，main，HEAD `86d6eccc0838fe0a896247a385087b774d7e6736`。开工时存在原 FE-01/FE-02 未提交成果；没有 reset、clean、stash、pull 或重建覆盖。
- 提示词原字节归档：[FE-02-closeout-v1.1.txt](../phases/FE-02-closeout-v1.1.txt)。旧规范、旧报告、旧失败性能记录全部保留。开工 178 个主题/证据文件 SHA-256 在 `output/fe02-closeout/baseline-hashes.json`，本轮差异见 `changes.json`。
- **产品源码仅修改父主题 `inc/search.php`**：验证后的 destination_card / ticket_reminder 外层 title/description 白名单；历史直接 shortcode 与单引号参数；投影指纹版本 `search-projection-2`；先删除注释/script/style，再解析可见 shortcode，避免隐藏源中的字段被纳入。没有改 UI/CSS/JS、查询 SQL、12 条分页、原文章事实或 CMS 合同。
- 旧报告不是本轮原始证据的替代。详细新证据集中在仓库 `output/fe02-closeout/`，可重放脚本与 JSON 均保留；无测试凭据复制到验收目录。

## 真实环境与测量

本轮新建 9491 Parent+Child+Tools、9492 Parent-only+Tools、9493 Parent+Child/Tools disabled；此前 9481/9482/9483 均未监听，没有停止原服务。WordPress 6.8.3、PHP WASM 8.3.32、Playground CLI 3.1.52、Node 24.14.0，单 worker。实际 DB 类 `WP_SQLite_DB`、SQLite 3.51.0；`db_version()` 返回的 8.0.38 是兼容值，**不是实测 MySQL**。未配置持久对象缓存，缓存命中率未知。Windows 11 26200、Intel Family 6 Model 158、8 逻辑 CPU；RAM 未采集。

9491 有 300 篇负载文章和额外功能 fixture；搜索 `loadfe02` 恰好 300 条、12/页。当前实际可见模板来自挂载的父/子主题文件；没有生产站点或 CMS 数据源。官方运行资源首次在受限网络失败，经权限升级启动成功。

### FS-09 / FS-13 / FS-25 / FS-31

- Chromium 147.0.7727.56：禁 JS 的文章 header 真实链接 → 空搜索表单 → 原生 Enter → 12 条 → 第 2 页 1 条 → 阅读 → 返回 → 按钮改词到零结果 → 改词命中。真实 URL、查询值、条目已记录于 `fe02close-browser.js.json`。
- WebKit 26.6：同一路径完整通过，最终证据 `fe02webkit-nojs.js.json`。首轮 `fe02webkit-browser.js.json` 后两次读取发生在导航完成前，保留 pass=false 原记录；补验明确等待 URL 和 load，未把自动化时序问题写成产品无 JS 失败。
- JS 开启、拒绝一切外部网络时，同样的搜索/分页/阅读/返回/改词流程通过，`fe02close-flow.js.json`；无 CMS/业务 API 请求。只拒绝外部请求，不 mock WordPress 页面或搜索结果。
- 测试专用 MU plugin 对主搜索 `posts_request` 注入不存在表的 SELECT；实际应用数据库错误分支返回 **503**、可编辑表单、安全重试文字，没有 0 条摘要/SQL/表名/堆栈。相同词撤销注入后 **200 / 300 条**；正常零结果为 **200 / 0 条**。`before.json`、`after.json`。开关仅本地文件，不存在公开 query 参数后门，收尾禁用并停止实例。
- 两路径各 2 次预热 + 30 次原始计时；起点为浏览器真实 click 捕获，终点为首个 rAF 中输入框可见、有焦点、可编辑；每次真实键入 x 验证。没有把 CLI RPC 往返或 sleep 计入结果。nearest-rank P95：Chromium dialog **15.4ms** / inline **14.3ms**；WebKit **23ms / 24ms**，均 ≤100ms。390×844，默认动画设置，搜索无入场动画；所有样本包括极值完整保留。
- Tab/Shift+Tab 循环、Escape、菜单互斥、焦点返回通过。Chromium 首轮立刻读取异步 close 事件前的 scroll lock 得到 false，保留原值；`fe02close-regression.js.json` 明确等事件后证实 dialog=false、lock=false、focus=true。
- 搜索 JS **725B gzip ≤8KiB**，CSS **1725B gzip ≤5KiB**；本轮未改资源，重新压缩核对一致。

### FS-32 同口径性能

下表均为同一实例、代码、语料，Python urllib 顺序完整 HTTP 响应时间；HTML 解析在计时外。每组 **30** 样本，预热单列；不同词不冒充冷缓存，重复词不冒充已证明的全层热缓存。没有重启 worker 的冷启动样本，没有可控清空 DB/OS 缓存，严格冷缓存 **NOT_MEASURED**。

| 组别 | HTTP P50 ms | HTTP P95 ms | PHP request P95 ms | 已采集 SQL P95 ms | 总查询次数 | 搜索 SQL P95 ms |
|---|---:|---:|---:|---:|---:|---:|
| 300 条结果 | 643.4 | **667.5** | 624.0 | 26.0 | 13 | 8.0 |
| 无结果 | 626.6 | 672.3 | 629.0 | 17.0 | 8 | 8.0 |
| 首页 | 674.9 | 703.7 | 640.0 | 35.0 | 20 | 0 |
| 普通文章 | 638.9 | 656.0 | 619.0 | 20.0 | 12 | 0 |
| 30 个不同双词查询 | 639.3 | 672.3 | 640.0 | 29.0 | 13 | 12.0 |

`performance.json` 保存全部样本和 PHP/SQL/模板/峰值内存。每个结果请求仅 1 条包含投影子查询的搜索 SQL；PHP 峰值 44,040,192B（42MiB），不是 Node/WASM 整体 RSS。结果页 template_redirect→shutdown P95 27ms，是整个模板阶段，**不是单独卡片或摘要生成耗时**。投影在保存时生成，本轮未单独计量其生成时间。

`SAVEQUERIES` 在测试 MU plugin 启用，SQL 毫秒只包含其启用后可采集的查询；总查询计数来自 `$wpdb->num_queries`。PHP request 使用 REQUEST_TIME_FLOAT→shutdown；probe 起点较晚，不能把前段全部归因于 Playground。探针前的早期 PHP/WordPress/SQLite 初始化未进一步拆分，PHP 毫秒时钟亦有约 1ms 量化。采样与日志本身有开销，另用 `unprofiled.json` 保留禁用探针的 30 次同查询观测；非随机交叉实验，不能将组间差值全部解释为探针开销。

原环境 779.8/761.3ms 失败记录没有删除；这轮不同 worker/环境状态的 667.5ms **不能宣称代码优化收益**。所有对照组均有较大共同前段开销，搜索 SQL 不是已证实的主瓶颈；因此未做无证据的 JOIN/索引/schema/缓存重写。当前判定 **FAIL_TARGET（Playground 完整 HTTP、500ms）**。原生 PHP/DB 对照环境未提供，本机未发现 php/docker/mysql/mariadb 命令，**PENDING_ENV/NOT_MEASURED**；不推断生产速度，不申请付费云服务。

关闭探针的独立观测：30 次、P50 **654.6ms**、P95 **671.8ms**，仍未达 500ms。查询路径代码在两组间未变；此后仅调整保存时隐藏源过滤顺序，不影响已测查询路径。

## 当前 FS-01 至 FS-34 判定表

“沿用”指相应代码/输入未受本轮变更影响，复核旧证据后保留；不表示本轮机械重跑全部 UI。

| ID | 原状态 | 本轮依据/修正 | 当前状态 | 剩余分类 / 影响 |
|---|---|---|---|---|
| FS-01 | PASS_LOCAL | 原 browser JSON 1280/1440 + 原截图，UI hash 未变 | PASS_LOCAL | 无 |
| FS-02 | PASS_LOCAL | 原 320–1024 viewport JSON，UI 未变 | PASS_LOCAL | 无 |
| FS-03 | PASS_LOCAL | 新 inline/dialog 分路径真实交互 | PASS_LOCAL | 无 |
| FS-04 | PASS_LOCAL | 原 375/390/430 布局值，栏目代码未改 | PASS_LOCAL | 无 |
| FS-05 | PASS_LOCAL | 原表单尺寸、label；新真实可编辑流程 | PASS_LOCAL | 无 |
| FS-06 | PASS_STATIC | 没有新增 UI 库/外部字体 | PASS_STATIC | 无 |
| FS-07 | PASS_LOCAL | 两引擎循环/关闭焦点；异步 close 补验 | PASS_LOCAL | 无 |
| FS-08 | PASS_LOCAL | 菜单互斥、滚动恢复新证据 | PASS_LOCAL | 无 |
| FS-09 | PARTIAL_LOCAL | Chromium/WebKit 禁 JS 全链路 | PASS_LOCAL | 无 |
| FS-10 | PARTIAL_LOCAL | 保留 WebKit 视口模拟，非真机键盘 | PARTIAL_LOCAL | DEVICE_NOT_TESTED / 真 iPhone |
| FS-11 | PARTIAL_LOCAL | 原 200% 页面字体模拟保留 | PARTIAL_LOCAL | DEVICE_NOT_TESTED / 系统大字 |
| FS-12 | PASS_LOCAL | 新空入口真实表单，无全站循环 | PASS_LOCAL | 无 |
| FS-13 | PASS_LOCAL | 新 JS/无 JS 第2页、阅读、返回、改词 | PASS_LOCAL | 无 |
| FS-14 | PASS_LOCAL | 原 0/1/12/13；本轮 13=12+1、0、1 | PASS_LOCAL | 无 |
| FS-15 | PASS_LOCAL | 新动态正文 draft/private/future/password 排除 | PASS_LOCAL | 无 |
| FS-16 | PASS_LOCAL | query 参数注入不扩大集合 | PASS_LOCAL | 无 |
| FS-17 | PASS_LOCAL | SQL post 限制未改，原类型证据沿用 | PASS_LOCAL | 无 |
| FS-18 | PASS_LOCAL | 原 cookie-admin 证据；新应用密码请求不越权（不混同登录方式） | PASS_LOCAL | 无 |
| FS-19 | PASS_LOCAL | title/excerpt/body 三独特词真实 GET | PASS_LOCAL | 无 |
| FS-20 | PARTIAL_LOCAL | 路线六字段、注释三字段、修复四字段；地点标题/说明仍漏搜 | **PARTIAL_LOCAL** | **LOCAL_FUNCTION_FAILED** / 核心覆盖；媒体/关联失效缺口；见字段清单 |
| FS-21 | PASS_LOCAL | 新状态/编辑/删除失效，本地幂等回填证明 | PASS_LOCAL（文章自身） | WAITING_AUTH_PRODUCTION；关联依赖缺口归 FS-20 |
| FS-22 | PARTIAL_LOCAL | 标题优先且稳定；合法中文/撇号/连字符命中 | PASS_LOCAL | NLP 不属原要求，不增做同义/纠错 |
| FS-23 | PASS_LOCAL | 300 条/12条/25页原证据；新分页及 SQL 级计数 | PASS_LOCAL | 无 |
| FS-24 | PASS_LOCAL | 新数组与121字符400，合法标点/中文 | PASS_LOCAL | 无 |
| FS-25 | PARTIAL_LOCAL | 真实主查询失败503→撤销200；0结果独立200 | PASS_LOCAL | 无 |
| FS-26 | PASS_STATIC | 产品无词日志；测试探针仅隔离实例 | PASS_STATIC | 无 |
| FS-27 | PASS_LOCAL | 卡片模板/样式未改，原资源/布局证据沿用 | PASS_LOCAL | 无 |
| FS-28 | PASS_LOCAL | 新空/结果/零/第二页真实 H1 与表单 | PASS_LOCAL | 无 |
| FS-29 | PASS_LOCAL | 原默认栈 noindex 证据沿用，真实插件未知 | PASS_LOCAL（本地默认栈） | PENDING_ENV / 真实 SEO 组合 |
| FS-30 | PARTIAL_LOCAL | 默认栈普通页面合同回归通过 | PARTIAL_LOCAL | PENDING_ENV / 真实插件 robots/canonical/sitemap |
| FS-31 | PARTIAL_LOCAL | 两引擎各2路径30次；gzip预算复核 | PASS_LOCAL | ≤100ms；资源预算通过 |
| FS-32 | FAIL_TARGET | 同口径5组×30、PHP/SQL/内存分层 | **FAIL_TARGET** | 500ms未达；严格冷缓存/原生环境 NOT_MEASURED |
| FS-33 | PASS_LOCAL | 拒绝外部网络全流程、运行时合同、两组合新增字段 | PASS_LOCAL | 无 CMS 实库依赖 |
| FS-34 | PARTIAL_LOCAL | 规定静态/运行时检查通过；两组合真实 PHP 执行 | PARTIAL_LOCAL | PENDING_ENV / 真 Safari、辅助技术；没有独立 PHP CLI 不否定 WASM 执行 |

## 交付、阻塞与停止

- 字段覆盖和准确缺口：[FE-02-projection-inventory.md](FE-02-projection-inventory.md)。FS-20 最小续修为前端的媒体/实体反向依赖、版本和安全失效；无需先访问生产或 CMS。现有安全边界保留，但不能因为安全边界就将核心漏搜判 PASS。
- 生产回填、并发保护、canary、回滚和上线条件：[FE-02-production-plan.md](FE-02-production-plan.md)。本地证明了幂等、断点继续、应用前编辑版本拒绝与原字段不变；**未证明比较/写入竞争窗口安全**，生产执行器须先补齐，不可直接搬用旧 REST 重保存脚本。
- FS-32 无已证实可优化的搜索代码瓶颈；下一最小步骤是提供/批准常规 PHP+DB 隔离环境同语料对照，或用户明确决定是否接受指定环境偏差。目标本身仍未达到。
- FS-10/11/29/30/34 的真实设备、系统字体、屏幕阅读器与准确 SEO 插件版本单列外部依赖；本机缓存未发现 axe，不临时安装工具来冒充人工验收。
- 验证命令：`python output/fe02-closeout/check.py after`、`state.py`、`hidden.py`、`combos.py`、`measure.py`；`node output/fe02-closeout/run-browser.cjs <session> <script>`；`pwsh -NoProfile -File scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9491`；`git diff --check`、`node --check .../search.js`。日志与首次测试脚本错误在 `output/fe02-closeout/` 保留。
- 操作事实：CMS modified **否**；commit/push/merge/deploy **否**；production read/write **否**；paid call **否**。写入仅仓库文件及隔离合成 WordPress 数据。未触碰 CMS 任务。
- `current_authorized_step=NONE`，`phase_end_stop=true`。本轮测试服务/浏览器停止情况以 `output/fe02-closeout/cleanup.json` 为准；只停止本轮创建进程，保留目录与证据，无后续后台任务。

分层结论：本轮局部修复、补测与文档已交付；**不能称 FE-02 全部本地功能验收完成**。核心正文 FS-20 未全覆盖，性能 FS-32 未达标；生产索引尚未授权，真实设备/插件尚待指定环境。请先评审这些结果，后续回填或上线必须另行明确批准对应计划。
