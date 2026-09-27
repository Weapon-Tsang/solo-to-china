# 2026-09-27 最新决策覆盖说明

原v1.2实现、原始样本及FS矩阵保留。当前FS-32/performance_decision为 **ACCEPTED_DEVIATION_BY_USER**：原P95约671.3ms、原目标500ms数值未达到，历史FAIL_TARGET不改。旧“无偏差授权／需性能验证”仅是当时记录，已被用户本轮决定解决，取消未执行专项性能及新环境计划。其余本地PASS复用，目标SQL/缓存、设备/SEO和生产权限各自单列。

当前六项结论与编号对照见[最终核查](FE-02-release-precheck.md)，唯一操作依据见[发布计划](FE-02-production-plan.md)。本轮未修改产品源码或应用版本、未重跑旧测试、未生产读取/写入或上线。

---

## 以下为历史记录（当前决策以上文为准）

# FE-02 v1.2 本地续修验收（2026-09-27）

本报告接续 v1.1，旧规范、旧报告、失败样本和未提交成果均保留。**本期核心功能与本地回填安全通过；HTTP 性能仍 FAIL_TARGET，生产未执行。** 不声明 FE-02 全部交付完成。

## 实施范围与存储协议

本轮产品代码只涉及父主题 `inc/search.php`、新增 `inc/search-projection.php` 和派生数据执行库 `scripts/search-backfill.php`。没有修改 UI、CSS、JS、栏目布局、CMS 合同或文章事实。

复现证据：`output/fe02-v12/before-fields.json` 的地点标题/说明在实际 renderer 输出可见，而 `before-http.json` 中匿名 GET 均为 0。`before-race.json` 在旧写入边界调用真实 `wp_update_post` 后，原文为新词，旧投影却覆盖了新投影；HTTP 仍命中旧词。该首次复现是可控重入，不冒充双进程验证。

规则升级为 `search-projection-3`。两张 WordPress 前缀表分别保存每篇当前投影，以及 generation 对应的资源版本；没有复制原始文章/payload 或建立外部搜索服务。`_stc_search_epoch_post:<ID>`、`entity:<key>`、`capability:tools` 是不自动加载的 option 版本标记。依赖表记录可读资源身份和随机版本，不把内部 ID、来源证据等拼入可搜索文本。

生成前读取依赖版本，构建候选后先持久化其不可变依赖，再通过**单条条件 INSERT/UPDATE**发布：文章仍公开、无密码，所有依赖版本一致，上一代 generation 未变。竞争者更新成功后，旧候选无法覆盖新 generation。文章保存、元数据绑定/解绑、依赖删除、目录切换均推进版本；查询中的版本检查发生在 SQL 分页与计数之前，没有事后删卡片。

规则与 renderer/helper 文件 SHA、Registry 版本和实际 Tools 能力共同约束有效性。旧两项 v2 meta 留档但不再作为正文检索依据；不自动声称旧文已迁移。未建表或尚未回填时，仍公开的 title/excerpt 可搜。静态文章不因 Tools 开关单独失效。

相关更新每请求最多同步重建 25 篇；超过预算或失败的投影由 SQL 拒绝，标题/摘要仍可命中。管理员在获准范围内调用 `stc_search_rebuild_batch(after, upper, <=25)` 或显式 manifest 执行库推进重建；无隐式 cron、无限重试或查询期全库重建。既有实体 resolver 的查找行为沿用，仅在生成相关组件时调用，未扩展为用户搜索期处理。

删除流程特别处理两个时序：`deleted_post` 在核心缓存清理之前；删除元数据又可能在文章行删除前触发中间重建。因此在实际删除后清理对象缓存并再次推进资源版本，阻断中间投影。首次与第二次失败原始 JSON 都保留。

## 本地证据与边界

证据根：仓库 `output/fe02-v12/`。环境为 WordPress 6.8.3、PHP WASM 8.3.32、Playground CLI 3.1.52、实际 `WP_SQLite_DB`/SQLite；兼容 `db_version()` 数字不视为 MySQL。9501 单 worker 用于功能/性能；9502 双 worker 用于重叠请求。没有持久对象缓存。原生 PHP/MySQL/MariaDB 未发现，未安装新栈。

执行库只接受至多 25 个明确 ID 的 manifest，不重保存文章。检查点为按 ID/generation 的 completed/conflicts/failed 分类账；冲突不推进成功范围。恢复时重新检查当前 generation，不盲信旧检查点。写入已完成但检查点未落盘，可用原 manifest 幂等恢复。检查点文件应置于站点公开目录之外；文件锁仅保护日志，公开安全来自 SQL 条件与资源版本，不依赖仅回填进程参与的锁。

候选依赖在失败或中断后可能留下孤立记录，保持非公开、不能单独产生命中；本轮不自动删除正在运行或未知候选。生产计划须明确保留期限与停机后有界清理范围。版本 option 和新表均是新增前端派生数据，需纳入生产迁移授权。

## 原 FS-01～34 当前矩阵

| FS | 当前判定 | 本轮证据或保留范围 |
|---|---|---|
| FS-01 | PASS_LOCAL（沿用） | header/断点/UI hash 未变 |
| FS-02 | PASS_LOCAL（沿用） | 320–1024 触控与溢出证据未受影响 |
| FS-03 | PASS_LOCAL（沿用） | inline/dialog 行为未改 |
| FS-04 | PASS_LOCAL（沿用） | 栏目首图位置未改 |
| FS-05 | PASS_LOCAL（沿用） | 表单/焦点/字号未改 |
| FS-06 | PASS_STATIC | 无新增外部资源或 UI 库 |
| FS-07 | PASS_LOCAL（沿用） | Chromium/WebKit 焦点与关闭 |
| FS-08 | PASS_LOCAL（沿用） | 菜单互斥与滚动恢复 |
| FS-09 | PASS_LOCAL（沿用） | v1.1 两引擎无 JS 全链 |
| FS-10 | PARTIAL_LOCAL / DEVICE_NOT_TESTED | 真实 iPhone 软键盘待测 |
| FS-11 | PARTIAL_LOCAL / DEVICE_NOT_TESTED | 系统级大字待测；已有 200% 模拟保留 |
| FS-12 | PASS_LOCAL（沿用） | 空入口分支未改 |
| FS-13 | PASS_LOCAL | 本轮 SQL 分页回归；既有 URL 往返保留 |
| FS-14 | PASS_LOCAL | 0/1/12/13、多页真实 GET |
| FS-15 | PASS_LOCAL | 公开状态、密码及依赖撤回 |
| FS-16 | PASS_LOCAL（沿用） | 公共请求强制参数范围未改 |
| FS-17 | PASS_LOCAL（沿用） | 仍强制 post/publish/无密码 |
| FS-18 | PASS_LOCAL（沿用） | 管理员公共搜索范围未扩大 |
| FS-19 | PASS_LOCAL | 正文新字段实际 GET；title/excerpt SQL 保留 |
| FS-20 | PASS_LOCAL | A～F：地点、媒体/实体、隐藏源、旧版本、准确失效 |
| FS-21 | PASS_LOCAL（SQLite）；目标DB待验 | 文章自身旧 PASS 保留；并发、依赖与恢复独立取证 |
| FS-22 | PASS_LOCAL | 标题相关性优先及稳定次序回归 |
| FS-23 | PASS_LOCAL | 300 条、25 页、12/13、重复命中、失效窗口 |
| FS-24 | PASS_LOCAL（沿用） | 规范化与安全回显代码未改 |
| FS-25 | PASS_LOCAL（沿用） | v1.1 真实 503 注入；本轮另测派生写入失败 |
| FS-26 | PASS_STATIC | 无产品查询日志/外部查询；测试仅 localhost |
| FS-27 | PASS_LOCAL（沿用） | 卡片与摘要模板未改 |
| FS-28 | PASS_LOCAL（沿用） | 结果/空态模板未改 |
| FS-29 | PASS_LOCAL（默认栈） | 实际 SEO 组合仍 PENDING_ENV |
| FS-30 | PARTIAL_LOCAL / PENDING_ENV | 真实插件 robots/canonical/sitemap 未测 |
| FS-31 | PASS_LOCAL（沿用） | JS 725B、CSS 1725B gzip；两引擎面板计时保留 |
| FS-32 | FAIL_TARGET | 原 500ms 门槛不变，见本轮性能数据 |
| FS-33 | PASS_LOCAL | 父/子/Tools 组合及完整现有运行时验证 |
| FS-34 | PARTIAL_LOCAL | 静态/PHP/WP 已测；真实设备、辅助技术、SEO 栈待测 |

面板 P95 沿用 Chromium dialog/inline 15.4/14.3ms、WebKit 23/24ms。资源 hash 与 v1.1 匹配，不重跑无影响 UI。旧性能 667.5ms（探针组）、671.8ms（无探针组）及更早 779.8/761.3ms 全部保留，不用跨环境差值宣称代码加速。

## 操作边界

CMS modified：否；commit/push/merge/deploy：否；production read/write：否；paid call：否。修改和数据写入仅本前端工作区及隔离合成 WordPress。没有安装新插件/运行栈，没有停止用户原进程。历史 REST 重保存脚本保留，但不是本轮推荐执行器。

部署、生产只读盘点/备份、生产回填必须分别获准。当前不请求这些操作，不把本地通过等同上线准入。性能可选下一步：提供/批准代表性原生 PHP＋目标 DB 隔离测量，或由用户明确接受指定环境已测偏差作为某一步准入例外；当前没有偏差授权。

## 最终结果与命令

| 分项 | 最终结果 | 证据/限制 |
|---|---|---|
| search_local_core | **PASS_LOCAL** | `regression.json` 59项；`catalog-fields.json` 地名与分别审核的到达字段实际GET/撤销；`combos.json` 父/子/Tools启停及实体解绑；原文hash不变 |
| backfill_executor_local_safety | **PASS_LOCAL（WP_SQLite_DB）** | `overlap.json` 9类真正重叠请求全部 SKIP_CHANGED；`recovery.json` 提交前后中断、重复执行、实际SQL写失败与重试；目标DB/持久缓存另验 |
| panel_and_assets | **PASS_LOCAL，沿用** | `assets.json` 的hash与旧记录一致；725B/1725B gzip；不把旧面板计时写成本轮重测 |
| search_http_performance | **FAIL_TARGET** | `performance.json` 单worker 9501，5预热＋30完整HTTP：P50 **657.2ms**、P95 **671.3ms**；门槛500ms；无偏差授权 |
| production_index | **NOT_EXECUTED / WAITING_AUTH** | 未迁移/部署/回填生产；旧文无v3投影时仅保证标题/摘要 |
| device_and_seo_stack | **DEVICE_NOT_TESTED / PENDING_ENV** | 真iPhone键盘、系统字体、辅助技术、实际SEO插件名/版本/组合未测；已通过模拟不被否定 |
| release_plan | **已更新、未激活** | 目标数据库验证、性能决定、生产范围与分别授权尚缺；不建议跳过这些条件上线 |

`overlap.json` 每例记录：候选比较通过、暂停时间、独立编辑请求起止、编辑返回后旧请求仍 pending、旧请求结束时间与最终搜索结果ID。覆盖文章 edit/private/password/delete，依赖 edit/private/password/delete，以及两个回填反向提交。两个真正的请求使用同一真实SQLite数据库；没有 mock 更新或搜索结果。只在测试脚本中使用唯一文件屏障，产品无公开调试 query 开关。

`fanout.json`：30篇共享依赖，更新执行1153ms、同步重建计数25（请求预算）；最后一次资源版本推进后仍失效的正文被拒绝，因此即时新词仅命中依赖文章本身，所有30篇父文章标题仍可搜。两次明确范围的25/5项重建后新词31条，12/12/7分页。未把“尝试重建25”误记为“25项已经current”。无无限重试或全库重写。

`performance.json`：30次样本全保留，5次预热单列，不裁剪极值；额外25页逐页核验300个唯一条目。`ranking.json`验证标题优先、同文标题/正文重复词不会重复卡片、13=12+1。严格冷缓存和原生目标DB **NOT_MEASURED**。并发实例已停止后才计时；没有把旧SQL P95 8ms套用到这次新SQL，也没有用局部SQL替代完整HTTP。此次671.3ms与历史671.8ms是不同实例状态，不宣称0.5ms优化收益。

最终执行命令与退出码（相对于仓库根，详细机器记录见 `output/fe02-v12/commands.json`）：

- `python output/fe02-v12/regression.py` → **0**，最终59项无失败。
- `python output/fe02-v12/catalog-fields.py`、`combos.py`、`fanout.py` → **0**。
- `python output/fe02-v12/overlap.py` → **0**，9类双worker重叠请求无失败。
- `python output/fe02-v12/client.py recovery`、`lint`、`after-setup` → **0**；PHP 8.3.32 TOKEN_PARSE覆盖3个产品文件。
- `python output/fe02-v12/performance.py` → **0**代表分页断言通过；JSON明确 **FAIL_TARGET**，不是性能通过。
- `pwsh -NoProfile -File scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9501` → **0**，静态/合同/内容与Tools运行时全通过，日志 `verify-upgrade-final.log`。
- `git diff --check` → **0**。本轮相对开工基线的产品差异保存在 `product.patch`，最终文件SHA及差异清单见 `changes.json`；完整工作树原有diff另存 `baseline.diff`。

本机仓库 root、remote、branch、HEAD 与输入报告一致：`C:\Users\Mloong\Documents\ChatGPT\solo-to-china`，`https://github.com/Weapon-Tsang/solo-to-china.git`，`main`，`86d6eccc0838fe0a896247a385087b774d7e6736`。输入报告标识 SHA `6bb21cd6013b6268bbfe61d14fbb5003d6c54952ff4e0fc6396fbc8acf4c6561` 仅用于识别上轮依据；未将后续文件恢复为旧字节。

旧命令失败、测试环境修正、首次真实缺陷与修复过程见 `attempts.md`。没有自动审批拒绝。已停止本轮9501/9502自建服务，精确PID核验与端口终态见 `cleanup.json`；所有旧目录与本轮证据保留。

`current_authorized_step=NONE`；`phase_end_stop=true`。本轮A/B停止，不启动新阶段或后台代理。
