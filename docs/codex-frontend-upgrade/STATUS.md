# FE-01 / FE-02 发布执行检查点（2026-09-27，当前有效）

- 源码已提交：`1c42a40f354899ba0e424a3590a3b1e880efff1e`，父主题 `0.33.12`、子主题 `0.13.2`；发布检查点文档提交 `703eece7a441e8c6322d82c68393be37eeb6690f`。已正常推送至 `origin/main` 并读回该 SHA；后续状态更新仅改文档，不改变制品。
- 已从该提交导出完整父/子主题 ZIP 与管理员回填库，逐文件与提交核对通过。准确 SHA、位置和结果见 [本次发布执行记录](acceptance/FE-01-FE-02-release-20260927.md)。旧 `dist` 保留。
- 生产部署、备份、schema、回填、维护和分支删除均未执行。已通过现有 WordPress 后台只读核实目标运行栈；缺 SSH/面板的服务器文件、SQL 与可恢复备份入口，目标 MariaDB 兼容测试尚未做。GitHub webhook、Actions 工作流与规则集均为 0，推送后公开主题版本仍未变化。
- 本地 `verify-project.ps1`、JS 语法、暂存差异检查通过；PHP CLI 在 PATH 不存在，沿用此前同一搜索实现的 WASM/SQLite 验证，版本字段改动未做目标 PHP/数据库验证。
- performance_decision: `ACCEPTED_DEVIATION_BY_USER`；历史 Playground/WASM/SQLite HTTP P95 约 `671.3ms`，500ms 旧目标仍为 `FAIL_TARGET`，本轮没有专项重测。
- current_authorized_step: `PRODUCTION_CONNECTION_REQUIRED`；没有活动生产维护或迁移进程。本检查点不代表正式交付完成。

---

# FE-02 最终收尾：性能偏差已接受（2026-09-27，历史检查点）

- result: LOCAL_CORE_PASS_PERFORMANCE_ACCEPTED_RELEASE_PLAN_READY
- search_local_core: **PASS_LOCAL_REUSED**。产品代码和既有原始证据未变；核心正文、公开权限、分页、真实查询错误及 SQLite 并发/恢复证据复用，不宣称本轮重新测试。
- performance_decision: **ACCEPTED_DEVIATION_BY_USER**。既有已测代码/Playground WASM/SQLite 完整 HTTP P95 约671.3ms；原500ms目标数值未达到，历史 FAIL_TARGET 和原样本保留。旧 PERF_DECISION_REQUIRED／无偏差授权已解决；不外推生产速度，不安排专项重测、性能环境安装或优化。
- target_correctness: **TARGET_COMPATIBILITY_NOT_TESTED**。SQLite证据有效；真实目标引擎/版本/SQL模式/存储引擎、DDL及条件写入、恢复和持久缓存未验。静态代码核对不是目标运行验证。
- index_readiness: **PLAN_READY / WAITING_AUTH_PRODUCTION**。沿用 search-projection-3 与每批最多25个明确ID的执行器；生产数量/ID未知，未读库存、未建表、未回填。无有效投影时仅保证仍公开文章的标题/摘要可搜。
- device_and_seo: **LOCAL_EVIDENCE_REUSED / TARGET_SEO_AND_DEVICE_NOT_TESTED**。Chromium/WebKit/无JS/焦点/缩放及默认SEO证据保留；实际SEO组合、真iPhone键盘、系统大字、辅助技术未测，不否定本地PASS。
- release_readiness: **READY_FOR_REVIEW / WAITING_AUTH_PRODUCTION**。目标兼容证据和生产操作权限仍缺；旧dist主题ZIP不是当前FE制品。
- current_authorized_step: `NONE`
- phase_end_stop: `true`

当前详细验收：[发布准备核查](acceptance/FE-02-release-precheck.md)。唯一执行顺序及集中申请：[发布计划](acceptance/FE-02-production-plan.md)；[索引运维](acceptance/FE-02-index-operations.md)；[精确文件和制品身份](acceptance/FE-02-release-files.md)。原始本轮输入已归档：[完整提示词](phases/FE-02-final-delivery-performance-accepted.txt)。下方所有旧性能待定、新环境/基准建议均为历史，已取消，不是后续任务。

---

## 以下为历史记录（当前决策以上文为准）

# FE-02 release precheck — 2026-09-27（当前状态）

- result: LOCAL_EVIDENCE_VERIFIED_RELEASE_PLAN_READY_TARGET_NOT_TESTED
- current_authorized_step: `NONE`
- phase_end_stop: `true`
- production_activation: `NOT_AUTHORIZED`
- 当前报告：[发布准备核查](acceptance/FE-02-release-precheck.md)；[运维与授权顺序](acceptance/FE-02-index-operations.md)；[精确文件清单](acceptance/FE-02-release-files.md)。
- 本轮只核验已有证据及完成发布准备：历史239个hash匹配，产品代码/合同/UI未修改；原SQLite PASS保留，目标DB/缓存NOT_TESTED；历史HTTP P95 671.3ms仍FAIL_TARGET。无新运行时、进程、生产访问、提交或部署。
- 原生候选环境与独立生产只读申请见当前报告；实际SEO与真机单列未测。旧记录完整保留在下方，不将旧阶段授权继承到本轮。

---

# SoloToChina 前端增量状态｜2026-09-27

- current_phase: FE-02 Site Search
- result: LOCAL_CORE_PASS_WITH_FAIL_TARGET_AND_PRODUCTION_PENDING
- current_authorized_step: `NONE`
- phase_end_stop: `true`
- next_suggested_phase: Representative native PHP/target-DB validation or explicit measured performance exception decision; production operations remain separately authorized
- source_of_truth: [Current FE-02 v1.2 acceptance](acceptance/FE-02-closeout-v1.2.md), [projection field inventory](acceptance/FE-02-projection-inventory.md), [production plan](acceptance/FE-02-production-plan.md), [FE-02 original prompt](phases/FE-02.txt), [FE-02 historical acceptance](acceptance/FE-02.md). FE-01 evidence remains in [FE-01 acceptance](acceptance/FE-01.md).

## FE-02 v1.2 当前结论（以下旧记录保留为历史）

- search_local_core：PASS_LOCAL；FS-20 核心地点文字、媒体/实体版本、删除/撤回/密码/解绑、旧规则投影均已补齐真实 GET 证据。59项回归通过；静态/模板/Tools 运行时检查通过。
- backfill_executor_local_safety：PASS_LOCAL（WP_SQLite_DB）；9类双worker真实重叠请求均拒绝旧提交，原文与新投影保持；恢复/幂等/失败后重试及分类检查点通过。原生目标DB与持久对象缓存仍 PENDING_ENV。
- panel_and_assets：UI 未改；复用 v1.1 计时，JS/CSS 725B/1725B gzip，hash一致。
- search_http_performance：单worker Playground/WASM/SQLite、300结果、5次预热＋30次完整HTTP，P50 657.2ms、P95 671.3ms，**FAIL_TARGET（500ms）**；25页无重复/遗漏。无用户偏差授权，不推断生产速度。
- production_index：NOT_EXECUTED / WAITING_AUTH。旧文未做v3回填时只保证公开标题/摘要；两张派生表与epoch options为新增迁移范围，不把旧v2文本静默认作current。
- device_and_seo_stack：本地默认栈证据保留；真iPhone键盘、系统大字、辅助技术及准确SEO组合待验。
- release_plan：v1.2计划已更新；未部署、未读取生产、未改CMS、未commit/push/merge、未付费调用。下一步仅列代表性环境测量或明确的工程偏差决定，不自动执行。
- 本轮产品差异：`inc/search.php`、新增`inc/search-projection.php`、新增`scripts/search-backfill.php`。基线、失败记录、原始JSON、命令与清理证据保留在`output/fe02-v12/`；旧成果未覆盖或删除。
- current_authorized_step: `NONE`；phase_end_stop: `true`。进程最终停止以`output/fe02-v12/cleanup.json`为准，无后续后台任务。

## 代码与工作区

- Git：`C:\Users\Mloong\Documents\ChatGPT\solo-to-china`；`origin=https://github.com/Weapon-Tsang/solo-to-china.git`；`main`；`HEAD=86d6eccc0838fe0a896247a385087b774d7e6736`。
- 版本：Parent `0.33.11`、Child `0.13.1`；未改应用版本、CMS 合同或生成文件。
- 修改但未提交：`scripts/start-preview.ps1`、`scripts/verify-project.php`、`scripts/verify-project.ps1`、`wp-content/themes/solo-to-china-child/assets/css/site.css`、`wp-content/themes/solo-to-china-child/header.php`、`wp-content/themes/solo-to-china/archive.php`、`wp-content/themes/solo-to-china/assets/css/main.css`、`wp-content/themes/solo-to-china/functions.php`、`wp-content/themes/solo-to-china/header.php`、`wp-content/themes/solo-to-china/index.php`、`wp-content/themes/solo-to-china/page.php`。
- 新增未跟踪：本 `docs/codex-frontend-upgrade/`。`output/` 开工前已有用户的两张生产首页参考图；本轮又产生了忽略的本地测试辅助文件与独立基线副本，未删除用户原文件。`git diff --stat`：11 files changed, 158 insertions(+), 43 deletions(-)（不含新文档/截图）。

## 需求与测试状态

- COL-001～COL-014：已实施并按 FC 对应实测。
- FC-01～FC-22：全部 `PASS_LOCAL`；逐项条件、环境与证据见 [FE-01.md](acceptance/FE-01.md)。无 `BLOCKED` 或 `PENDING_ENV` 本地硬项。
- 静态/合同/工具/运行时：最终 `pwsh scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9471` 通过。PHP WASM 8.3.32 的 44 文件语法扫描通过。
- 浏览器：真实本地 WordPress 的 Parent+Child+Tools、Parent-only、Child 无 Tools；28 个主视口/路由组合及空/单篇/多篇/长标题/无图/第二页/分享/TOC/键盘菜单/200% root 字号测试完成。原始 JSON 在 `acceptance/`，截图在 `screenshots/`。
- 390px 首图 top：City 基线 806.6 → 当前 141.6；Attraction 920.7 → 141.6；City 分类 288.7 → 141.6 CSS px。375/390/430 当前四类路由均 141.6px。
- 生产：未验证、未发布；本地 LCP 观测不可等同线上 Core Web Vitals。CMS 阶段与数据未接触。
- 本轮 WordPress 预览端口 9471/9472/9473/9474 与 Playwright 浏览器均已停止。未停止其他任务进程。
- 自动审批策略拦截了递归删除本轮 `output/` 测试副本的清理命令；这些忽略文件保留在本仓库 `output/`，不影响源码、报告或测试结论。未尝试绕过拦截。

## 实际操作边界

CMS modified：否；commit：否；push：否；merge：否；deploy：否；production write：否；paid model call：否。仅隔离本地 WordPress fixture 被测试脚本修改。

## FE-02 local completion (2026-09-27)

- The search UI, public query guardrails, pagination, noindex, and safe visible-text projection are implemented in the frontend theme. Parent, child, and Tools-disabled local combinations passed core route checks.
- Detailed FS-01 to FS-34 results: [FE-02 acceptance](acceptance/FE-02.md). Browser measurements: [FE-02-browser.json](acceptance/FE-02-browser.json). Performance measurements: [FE-02-performance.json](acceptance/FE-02-performance.json).
- The local 300-post HTTP P95 exceeded the 500 ms target. Production timing, real SEO plugin/device combinations, database error injection, and optional dynamic content projection remain open environment items.
- Existing published posts need a separately authorized backfill of the safe search projection for body terms. No production write was made.
- FE-01 PASS_LOCAL evidence remains valid. No commit, push, merge, deployment, CMS edit, or production write occurred in FE-02.
- current_authorized_step: NONE; phase_end_stop: true.
